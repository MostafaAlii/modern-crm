<?php
namespace App\Models\Concerns;
use App\Models\RefreshToken;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
trait HasRefreshToken {
    /**
     * Get all refresh tokens for this user
     */
    public function refreshTokens(): MorphMany {
        return $this->morphMany(RefreshToken::class, 'user');
    }

    /**
     * Create a new refresh token
     */
    public function createRefreshToken(array $data = []): RefreshToken {
        $defaults = [
            'token_id' => Str::uuid()->toString(),
            'access_token_id' => $data['access_token_id'] ?? null,
            'device_info' => $data['device_info'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'location' => $data['location'] ?? null,
            'expires_at' => $data['expires_at'] ?? now()->addDays(config('jwt.refresh_ttl', 30)),
            'metadata' => $data['metadata'] ?? [],
        ];
        return $this->refreshTokens()->create($defaults);
    }

    /**
     * Revoke all refresh tokens for this user
     */
    public function revokeRefreshTokens(?int $revokedBy = null): bool {
        return $this->refreshTokens()->valid()->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
            ]) > 0;
    }

    /**
     * Revoke a specific refresh token by token_id
     */
    public function revokeRefreshToken(string $tokenId, ?int $revokedBy = null): bool {
        $token = $this->refreshTokens()->where('token_id', $tokenId)->first();
        if (!$token) {
            return false;
        }
        return $token->revoke($revokedBy);
    }

    /**
     * Revoke all refresh tokens except the given one
     */
    public function revokeOtherRefreshTokens(string $exceptTokenId, ?int $revokedBy = null): bool {
        return $this->refreshTokens()->where('token_id', '!=', $exceptTokenId)->valid()
            ->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
            ]) > 0;
    }

    /**
     * Revoke refresh tokens by device info
     */
    public function revokeRefreshTokensByDevice(string $deviceInfo, ?int $revokedBy = null): bool {
        return $this->refreshTokens()->byDevice($deviceInfo)->valid()->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
            ]) > 0;
    }

    /**
     * Revoke refresh tokens by IP address
     */
    public function revokeRefreshTokensByIp(string $ipAddress, ?int $revokedBy = null): bool {
        return $this->refreshTokens()->byIp($ipAddress)->valid()
            ->update([
                'is_revoked' => true,
                'revoked_at' => now(),
                'revoked_by' => $revokedBy,
            ]) > 0;
    }

    /**
     * Find a valid refresh token by token_id
     */
    public function findValidRefreshToken(string $tokenId): ?RefreshToken {
        return $this->refreshTokens()->where('token_id', $tokenId)->valid()->first();
    }

    /**
     * Get all valid refresh tokens
     */
    public function getValidRefreshTokens() {
        return $this->refreshTokens()->valid()->get();
    }

    /**
     * Get all revoked refresh tokens
     */
    public function getRevokedRefreshTokens() {
        return $this->refreshTokens()->revoked()->get();
    }

    /**
     * Get all expired refresh tokens
     */
    public function getExpiredRefreshTokens() {
        return $this->refreshTokens()->expired()->get();
    }

    /**
     * Check if user has any valid refresh tokens
     */
    public function hasValidRefreshTokens(): bool {
        return $this->refreshTokens()->valid()->exists();
    }

    /**
     * Clean up expired refresh tokens
     */
    public function cleanupExpiredRefreshTokens(): int {
        return $this->refreshTokens()->expired()->delete();
    }

    /**
     * Get devices with active sessions
     */
    public function getActiveDevices(): array {
        return $this->refreshTokens()
            ->valid()
            ->select(['device_info', 'ip_address', 'user_agent', 'last_used_at', 'created_at'])
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Update device info for a specific token
     */
    public function updateDeviceInfo(string $tokenId, array $deviceInfo): bool {
        $token = $this->refreshTokens()->where('token_id', $tokenId)->first();
        if (!$token) {
            return false;
        }
        return $token->update([
            'device_info' => json_encode($deviceInfo),
        ]);
    }
}
