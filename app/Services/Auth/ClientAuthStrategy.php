<?php
namespace App\Services\Auth;
use Illuminate\Support\Facades\Auth;
class ClientAuthStrategy implements AuthStrategyInterface {
    public function login(array $credentials): bool {
        return Auth::guard('client')->attempt($credentials);
    }

    public function logout(): void {
        Auth::guard('client')->logout();
    }
}