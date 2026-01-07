<?php
namespace App\Services\Auth;
class AuthService {
    public function __construct(protected AuthStrategyInterface $strategy) {
        $this->strategy = $strategy;
    }

    public function login(array $credentials): bool {
        return $this->strategy->login($credentials);
    }

    public function logout(): void {
        $this->strategy->logout();
    }
}