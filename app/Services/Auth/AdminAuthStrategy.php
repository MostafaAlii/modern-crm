<?php
namespace App\Services\Auth;
use Illuminate\Support\Facades\Auth;
class AdminAuthStrategy implements AuthStrategyInterface {
    public function login(array $credentials): bool {
        return Auth::guard('admin')->attempt($credentials);
    }

    public function logout(): void {
        Auth::guard('admin')->logout();
    }
}