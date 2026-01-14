<?php
namespace App\Services\Auth;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\Result\AuthResult;
use App\Enums\Admin\{AdminStatus};
use App\Enums\Client\{ClientStatus};
use App\Services\Auth\Strategy;
class OldAuthService {
    protected array $strategyMap = [];
    protected array $inactiveStatuses = [
        AdminStatus::IN_ACTIVE->value,
        ClientStatus::IN_ACTIVE->value,
    ];
    public function __construct(protected GuardResolver $guardResolver) {
        $this->strategyMap = [
            'admin' => new Strategy\AdminAuthStrategy(),
            'client' => new Strategy\ClientAuthStrategy(),
        ];
    }

    public function login(string $guard, array $credentials): AuthResult {
        $strategy = $this->strategyMap[$guard] ?? null;
        if (!$strategy) {
            return new AuthResult(false, trans('auth.guard_not_found'));
        }

        $user = $strategy->attempt($credentials);
        if (!$user) {
            return new AuthResult(false, trans('dashboard/general.invalid_credentials'));
        }

        if ($guard === 'admin' && $user->status !== AdminStatus::ACTIVE) {
            return new AuthResult(false, trans('dashboard/general.in_active_msg'));
        }

        if ($guard === 'client') {
            if ($user->status === ClientStatus::IN_ACTIVE) {
                return new AuthResult(false, trans('dashboard/general.in_active_msg'));
            }

            if ($user->status === ClientStatus::BLOCKED) {
                return new AuthResult(false, trans('dashboard/general.blocked_msg'));
            }

            if ($user->status === ClientStatus::SUSPENDED) {
                return new AuthResult(false, trans('dashboard/general.suspended_msg'));
            }
        }
        $strategy->loginUser($user);
        return new AuthResult(true, null, $user);
    }

    public function logout(string $guard): void {
        Auth::guard($guard)->logout();
    }
}
