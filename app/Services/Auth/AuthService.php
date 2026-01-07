<?php
namespace App\Services\Auth;
use Illuminate\Support\Facades\Auth;
class AuthService {
    public function login(string $guard, array $credentials): bool {
        return Auth::guard($guard)->attempt($credentials);
    }

    public function logout(string $guard): void {
        Auth::guard($guard)->logout();
    }
}