<?php
namespace App\Services\Auth;
use App\Models\RefreshToken;
use App\Models\Concerns\HasRefreshToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Cache,DB,Hash,Log};
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class TokenService {
    // Token expiration times (in minutes)
    const ACCESS_TOKEN_TTL = 60; // 1 hour
    const REFRESH_TOKEN_TTL = 60 * 24 * 30; // 30 days
    // Rate limiting
    const MAX_REFRESH_ATTEMPTS = 5;
    const REFRESH_RATE_LIMIT_WINDOW = 60; // 1 minute
    /**
     * Generate a new refresh token for user
     */
    public function generateRefreshToken(
        $user,
        ?string $accessTokenId = null,
        array $deviceData = []
    ): RefreshToken {
        // Clean up expired tokens first
        $this->cleanupExpiredTokens($user);
        // Generate token ID
        $tokenId = $this->generateTokenId();
        // Prepare device data
        $deviceInfo = $this->extractDeviceInfo(
            $deviceData['user_agent'] ?? request()->userAgent(),
            $deviceData['device_info'] ?? null
        );
        // Create refresh token
        $refreshToken = $user->createRefreshToken([
            'token_id' => $tokenId,
            'access_token_id' => $accessTokenId,
            'device_info' => $deviceInfo['device'],
            'ip_address' => $deviceData['ip_address'] ?? request()->ip(),
            'user_agent' => $deviceInfo['user_agent'],
            'location' => $this->getLocationFromIp($deviceData['ip_address'] ?? request()->ip()),
            'expires_at' => Carbon::now()->addMinutes(self::REFRESH_TOKEN_TTL),
            'metadata' => [
                'generated_at' => now()->toIso8601String(),
                'device_os' => $deviceInfo['os'],
                'device_browser' => $deviceInfo['browser'],
                'device_type' => $deviceInfo['type'],
            ],
        ]);
        // Log the token creation
        Log::info('Refresh token generated', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'token_id' => $tokenId,
            'device' => $deviceInfo['device'],
            'ip' => $deviceData['ip_address'] ?? request()->ip(),
        ]);
        return $refreshToken;
    }

    /**
     * Validate refresh token
     */
    public function validateRefreshToken(
        string $refreshTokenId,
        $expectedUser = null
    ): ?RefreshToken {
        // Check rate limiting
        if ($this->isRateLimited($refreshTokenId)) {
            Log::warning('Refresh token rate limited', ['token_id' => $refreshTokenId]);
            return null;
        }
        // Find the token
        $refreshToken = RefreshToken::where('token_id', $refreshTokenId)->first();
        if (!$refreshToken) {
            Log::warning('Refresh token not found', ['token_id' => $refreshTokenId]);
            $this->incrementRateLimit($refreshTokenId);
            return null;
        }
        // Check if token is valid
        if (!$refreshToken->isValid()) {
            $reason = $refreshToken->is_revoked ? 'revoked' : 'expired';
            Log::warning('Refresh token invalid', [
                'token_id' => $refreshTokenId,
                'reason' => $reason,
            ]);
            $this->incrementRateLimit($refreshTokenId);
            return null;
        }
        // Verify user if provided
        if (
            $expectedUser &&
            ($refreshToken->user_id != $expectedUser->id ||
                $refreshToken->user_type != get_class($expectedUser))
        ) {
            Log::warning('Refresh token user mismatch', [
                'token_id' => $refreshTokenId,
                'expected_user' => $expectedUser->id,
                'actual_user' => $refreshToken->user_id,
            ]);
            $this->incrementRateLimit($refreshTokenId);
            return null;
        }
        // Update last used timestamp
        $refreshToken->markAsUsed();
        // Reset rate limit on successful validation
        $this->resetRateLimit($refreshTokenId);
        return $refreshToken;
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(string $refreshTokenId): array {
        // Validate refresh token
        $refreshToken = $this->validateRefreshToken($refreshTokenId);
        if (!$refreshToken) {
            throw new \Exception('Invalid refresh token', 401);
        }
        $user = $refreshToken->user;
        // Generate new access token
        $accessToken = $this->generateAccessToken($user);
        $accessTokenPayload = JWTAuth::setToken($accessToken)->getPayload();
        $accessTokenId = $accessTokenPayload->get('jti');
        // Create new refresh token (token rotation)
        $newRefreshToken = $this->rotateRefreshToken($refreshToken, $accessTokenId);
        // Revoke the old refresh token
        $refreshToken->revoke();
        return [
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken->token_id,
            'access_token_id' => $accessTokenId,
            'expires_at' => Carbon::createFromTimestamp($accessTokenPayload->get('exp')),
            'token_type' => 'bearer',
            'user' => $user,
        ];
    }

    /**
     * Rotate refresh token (create new and revoke old)
     */
    public function rotateRefreshToken(RefreshToken $oldToken, ?string $newAccessTokenId = null): RefreshToken {
        $user = $oldToken->user;
        $deviceArray = [];
        if ($oldToken->device_info && is_string($oldToken->device_info)) {
            $deviceArray = json_decode($oldToken->device_info, true) ?? [];
        } elseif (is_array($oldToken->device_info)) {
            $deviceArray = $oldToken->device_info;
        }
        // Create new refresh token
        $newToken = $this->generateRefreshToken($user, $newAccessTokenId, [
            'device_info' => $deviceArray,
            'ip_address' => $oldToken->ip_address,
            'user_agent' => $oldToken->user_agent,
        ]);
        // Copy metadata from old token
        if ($oldToken->metadata) {
            $newToken->update([
                'metadata' => array_merge($oldToken->metadata, [
                    'rotated_from' => $oldToken->token_id,
                    'rotated_at' => now()->toIso8601String(),
                ]),
            ]);
        }
        Log::info('Refresh token rotated', [
            'old_token_id' => $oldToken->token_id,
            'new_token_id' => $newToken->token_id,
            'user_id' => $user->id,
        ]);
        return $newToken;
    }

    /**
     * Revoke a specific refresh token
     */
    public function revokeRefreshToken(
        string $refreshTokenId,
        ?int $revokedBy = null,
        string $reason = 'user_request'
    ): bool {
        $refreshToken = RefreshToken::where('token_id', $refreshTokenId)->first();
        if (!$refreshToken) {
            return false;
        }
        if ($refreshToken->is_revoked) {
            return true; // Already revoked
        }
        // Add reason to metadata
        $metadata = $refreshToken->metadata ?? [];
        $metadata['revocation_reason'] = $reason;
        $metadata['revoked_by_user'] = $revokedBy;
        $refreshToken->update([
            'metadata' => $metadata,
        ]);
        $result = $refreshToken->revoke($revokedBy);
        if ($result) {
            Log::info('Refresh token revoked', [
                'token_id' => $refreshTokenId,
                'user_id' => $refreshToken->user_id,
                'revoked_by' => $revokedBy,
                'reason' => $reason,
            ]);
        }
        return $result;
    }

    /**
     * Revoke all tokens for a user
     */
    public function revokeAllUserTokens(
        $user,
        ?int $revokedBy = null,
        string $reason = 'user_request'
    ): int {
        $count = $user->refreshTokens()
            ->valid()
            ->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
                'metadata' => DB::raw("JSON_SET(COALESCE(metadata, '{}'), '$.revocation_reason', '$reason')"),
            ]);
        Log::info('All user tokens revoked', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'count' => $count,
            'revoked_by' => $revokedBy,
            'reason' => $reason,
        ]);
        return $count;
    }

    /**
     * Revoke all tokens except one
     */
    public function revokeOtherTokens(
        $user,
        string $exceptTokenId,
        ?int $revokedBy = null,
        string $reason = 'keep_current_session'
    ): int {
        $count = $user->refreshTokens()
            ->where('token_id', '!=', $exceptTokenId)
            ->valid()
            ->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
                'metadata' => DB::raw("JSON_SET(COALESCE(metadata, '{}'), '$.revocation_reason', '$reason')"),
            ]);
        Log::info('Other tokens revoked', [
            'user_id' => $user->id,
            'except_token' => $exceptTokenId,
            'count' => $count,
            'reason' => $reason,
        ]);
        return $count;
    }

    /**
     * Get active sessions for user
     */
    public function getUserActiveSessions($user): array {
        $tokens = $user->refreshTokens()
            ->valid()
            ->orderBy('last_used_at', 'desc')
            ->get();
        return $tokens->map(function ($token) {
            return [
                'token_id' => $token->token_id,
                'device' => $token->device_info,
                'ip_address' => $token->ip_address,
                'location' => $token->location,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'usage_count' => $token->usage_count,
                'metadata' => $token->metadata,
            ];
        })->toArray();
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens($user = null): int {
        $query = RefreshToken::expired();
        if ($user) {
            $query->forUser($user->id, get_class($user));
        }
        $count = $query->delete();
        if ($count > 0) {
            Log::info('Expired tokens cleaned up', [
                'count' => $count,
                'user' => $user ? $user->id : 'all_users',
            ]);
        }
        return $count;
    }

    /**
     * Generate access token for user
     */
    protected function generateAccessToken($user): string {
        // Determine guard based on user type
        $guard = match (get_class($user)) {
            \App\Models\Admin::class => 'admin_api',
            \App\Models\Client::class => 'client_api',
            default => 'api',
        };
        try {
            return JWTAuth::claims([
                'guard' => $guard,
                'user_type' => get_class($user),
                'refresh_allowed' => true,
            ])->fromUser($user);
        } catch (\Exception $e) {
            return auth($guard)->login($user);
        }
    }

    /**
     * Generate unique token ID
     */
    protected function generateTokenId(): string {
        return hash('sha256', Str::uuid()->toString() . microtime(true) . random_bytes(16));
    }

    /**
     * Extract device info from user agent
     */
    protected function extractDeviceInfo(?string $userAgent = null, ?array $deviceData = null): array {
        $userAgent = $userAgent ?: request()->userAgent();
        if ($deviceData) {
            return [
                'device' => json_encode($deviceData),
                'user_agent' => $userAgent,
                'os' => $deviceData['os'] ?? 'unknown',
                'browser' => $deviceData['browser'] ?? 'unknown',
                'type' => $deviceData['type'] ?? 'desktop',
            ];
        }
        // Parse user agent string (simplified version)
        $os = 'unknown';
        $browser = 'unknown';
        $type = 'desktop';
        if (preg_match('/\((.*?)\)/', $userAgent, $matches)) {
            $os = $matches[1];
        }
        if (strpos($userAgent, 'Mobile') !== false) {
            $type = 'mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            $type = 'tablet';
        }
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }
        return [
            'device' => json_encode([
                'os' => $os,
                'browser' => $browser,
                'type' => $type,
                'raw' => $userAgent,
            ]),
            'user_agent' => $userAgent,
            'os' => $os,
            'browser' => $browser,
            'type' => $type,
        ];
    }

    /**
     * Get location from IP (simplified)
     */
    protected function getLocationFromIp(string $ip): ?string {
        // In production, use a service like ipinfo.io, MaxMind, etc.
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }
        // Simple implementation - in real app, use a proper geolocation service
        try {
            // This is a mock implementation
            $ipParts = explode('.', $ip);
            if (count($ipParts) === 4) {
                return "IP: {$ip}";
            }
        } catch (\Exception $e) {
            // Silent fail
        }
        return null;
    }

    /**
     * Check rate limiting for token
     */
    protected function isRateLimited(string $identifier): bool {
        $key = "refresh_rate_limit:{$identifier}";
        $attempts = Cache::get($key, 0);
        return $attempts >= self::MAX_REFRESH_ATTEMPTS;
    }

    /**
     * Increment rate limit counter
     */
    protected function incrementRateLimit(string $identifier): void {
        $key = "refresh_rate_limit:{$identifier}";
        Cache::increment($key);
        Cache::expire($key, self::REFRESH_RATE_LIMIT_WINDOW);
    }

    /**
     * Reset rate limit counter
     */
    protected function resetRateLimit(string $identifier): void {
        $key = "refresh_rate_limit:{$identifier}";
        Cache::forget($key);
    }

    /**
     * Get token statistics
     */
    public function getStatistics($user = null): array {
        $query = RefreshToken::query();
        if ($user) {
            $query->forUser($user->id, get_class($user));
        }
        return [
            'total' => $query->count(),
            'valid' => $query->valid()->count(),
            'revoked' => $query->revoked()->count(),
            'expired' => $query->expired()->count(),
            'by_device_type' => $this->getDeviceTypeStats($user),
        ];
    }

    /**
     * Get device type statistics
     */
    protected function getDeviceTypeStats($user = null): array {
        $query = RefreshToken::valid();
        if ($user) {
            $query->forUser($user->id, get_class($user));
        }
        $tokens = $query->get();
        $stats = [
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
            'unknown' => 0,
        ];
        foreach ($tokens as $token) {
            $metadata = $token->metadata ?? [];
            $type = $metadata['device_type'] ?? 'unknown';

            if (isset($stats[$type])) {
                $stats[$type]++;
            } else {
                $stats['unknown']++;
            }
        }
        return $stats;
    }
}