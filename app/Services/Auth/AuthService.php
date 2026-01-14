<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Services\Auth\Result\AuthResult;
use App\Enums\Admin\AdminStatus;
use App\Enums\Client\ClientStatus;
use App\Services\Auth\Strategy;

class AuthService
{
    protected array $strategyMap = [];
    protected array $inactiveStatuses = [
        AdminStatus::IN_ACTIVE->value,
        ClientStatus::IN_ACTIVE->value,
    ];

    public function __construct(protected GuardResolver $guardResolver)
    {
        // Strategy لكل نوع user
        $this->strategyMap = [
            'admin'  => new Strategy\AdminAuthStrategy(),
            'client' => new Strategy\ClientAuthStrategy(),
        ];
    }

    /**
     * Login حسب auth context
     *
     * @param array $authContext ['base','context','guard']
     * @param array $credentials ['email','password']
     * @return AuthResult
     */
    public function login(array $authContext, array $credentials): AuthResult
    {
        $baseGuard = $authContext['base'];
        $context   = $authContext['context']; // web | api
        $guard     = $authContext['guard'];   // admin / admin_api / client / client_api

        $strategy = $this->strategyMap[$baseGuard] ?? null;
        if (!$strategy) {
            return new AuthResult(false, trans('auth.guard_not_found'));
        }

        $user = $strategy->attempt($credentials);
        if (!$user) {
            return new AuthResult(false, trans('dashboard/general.invalid_credentials'));
        }

        // التحقق من الـ status حسب نوع المستخدم
        if ($baseGuard === 'admin' && $user->status !== AdminStatus::ACTIVE) {
            return new AuthResult(false, trans('dashboard/general.in_active_msg'));
        }

        if ($baseGuard === 'client') {
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

        // تسجيل الدخول حسب السياق
        $token = null;
        if ($context === 'web') {
            // Session login كما هو
            $strategy->loginUser($user);
        } elseif ($context === 'api') {
            // هنا هنولّد token لاحقًا بعد ما نثبت JWT
            // مؤقتاً حط null، بس جاهز للكود
            // مثال لاحق: $token = JWT::fromUser($user);
        }

        return new AuthResult(true, null, $user, $token);
    }

    /**
     * Logout حسب guard
     *
     * @param array $authContext
     * @return void
     */
    public function logout(array $authContext): void
    {
        $guard = $authContext['guard'] ?? 'web';
        Auth::guard($guard)->logout();

        // لاحقًا للـ API: نعمل invalidate للـ JWT
    }
}
