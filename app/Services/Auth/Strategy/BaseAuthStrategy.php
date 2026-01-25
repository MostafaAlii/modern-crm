<?php

namespace App\Services\Auth\Strategy;

use Illuminate\Support\Facades\{Auth, Hash};
use App\Services\Auth\AuthStrategyInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
abstract class BaseAuthStrategy implements AuthStrategyInterface {
    protected string $guard;
    protected string $modelClass;
    public function __construct(string $guard, string $modelClass) {
        $this->guard = $guard;
        $this->modelClass = $modelClass;
    }

    public function attempt(array $credentials): mixed {
        $userModel = $this->modelClass;
        $user = $userModel::where('email', $credentials['email'])->first();
        if (!$user) return null;
        if (!Hash::check($credentials['password'], $user->password)) return null;
        return $user;
    }

    public function loginUser(mixed $user, string $context = 'web'): ?string {
        if ($context === 'web') {
            Auth::guard($this->guard)->login($user);
            return null;
        }
        if ($context === 'api') {
            $token = JWTAuth::fromUser($user);
            return $token;
        }
        return null;
    }

    public function logout(): void {
        Auth::guard($this->guard)->logout();
    }
}
