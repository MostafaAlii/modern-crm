<?php

namespace App\Services\Auth;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\Result\AuthResult;
use App\Enums\Admin\AdminStatus;
use App\Enums\Client\ClientStatus;
use App\Services\Auth\Strategy;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\Auth\TokenService;
use Carbon\Carbon;
class AuthService {
    protected array $strategyMap = [];
    protected array $inactiveStatuses = [
        AdminStatus::IN_ACTIVE->value,
        ClientStatus::IN_ACTIVE->value,
    ];

    public function __construct(protected GuardResolver $guardResolver,protected TokenService $tokenService) {
        $this->strategyMap = [
            'admin'  => new Strategy\AdminAuthStrategy(),
            'client' => new Strategy\ClientAuthStrategy(),
        ];
    }

    public function login(array $authContext, array $credentials): AuthResult {
        $baseGuard = $authContext['base'];
        $context   = $authContext['context']; // web | api
        $guard     = $authContext['guard'];
        $strategy = $this->strategyMap[$baseGuard] ?? null;
        if (!$strategy) {
            return new AuthResult(false, trans('auth.guard_not_found'));
        }
        $user = $strategy->attempt($credentials);
        if (!$user) {
            return new AuthResult(false, trans('dashboard/general.invalid_credentials'));
        }
        $statusError = null;
        if ($baseGuard === 'admin' && $user->status !== AdminStatus::ACTIVE) {
            $statusError = trans('dashboard/general.in_active_msg');
        }

        if ($baseGuard === 'client') {
            if ($user->status === ClientStatus::IN_ACTIVE) {
                $statusError = trans('dashboard/general.in_active_msg');
            } elseif ($user->status === ClientStatus::BLOCKED) {
                $statusError = trans('dashboard/general.blocked_msg');
            } elseif ($user->status === ClientStatus::SUSPENDED) {
                $statusError = trans('dashboard/general.suspended_msg');
            }
        }

        if ($statusError !== null) {
            if ($context === 'web') {
                return new AuthResult(false, $statusError);
            }
            return new AuthResult(false, $statusError);
        }
        $accessToken = $strategy->loginUser($user, $context);
        $refreshToken = null;
        $expiresAt = null;
        if ($context === 'api' && $accessToken) {
            $payload = JWTAuth::setToken($accessToken)->getPayload();
            $expiresAt = Carbon::createFromTimestamp($payload->get('exp'));
            $accessTokenId = $payload->get('jti');
            $refreshTokenModel = $this->tokenService->generateRefreshToken(
                $user,
                $accessTokenId, [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            );
            $refreshToken = $refreshTokenModel->token_id;
        }
        return new AuthResult(true, null, $user, $accessToken, $expiresAt, $refreshToken);
    }

    public function logout(array $authContext): void {
        $guard = $authContext['guard'] ?? 'web';
        if (str_contains($guard, '_api')) {
            try {
                $token = JWTAuth::getToken();
                JWTAuth::invalidate($token);
                try {
                    $payload = JWTAuth::getPayload($token);
                    $accessTokenId = $payload->get('jti');
                    RefreshToken::where('access_token_id', $accessTokenId)
                        ->update(['is_revoked' => true, 'revoked_at' => now()]);
                } catch (\Exception $e) {
                }
            } catch (\Exception $e) {
            }
        } else {
            Auth::guard($guard)->logout();
        }
    }
}