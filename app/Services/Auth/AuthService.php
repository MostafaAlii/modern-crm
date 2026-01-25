<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Services\Auth\Result\AuthResult;
use App\Enums\Admin\AdminStatus;
use App\Enums\Client\ClientStatus;
use App\Services\Auth\Strategy;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthService {
    protected array $strategyMap = [];
    protected array $inactiveStatuses = [
        AdminStatus::IN_ACTIVE->value,
        ClientStatus::IN_ACTIVE->value,
    ];

    public function __construct(protected GuardResolver $guardResolver) {
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
        $token = $strategy->loginUser($user, $context);
        return new AuthResult(true, null, $user, $token);
    }

    public function logout(array $authContext): void {
        $guard = $authContext['guard'] ?? 'web';
        if (str_contains($guard, '_api')) {
            try {
                JWTAuth::invalidate(JWTAuth::getToken());
            } catch (\Exception $e) {
            }
        } else {
            Auth::guard($guard)->logout();
        }
    }
}
