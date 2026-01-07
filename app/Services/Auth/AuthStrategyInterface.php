<?php
namespace App\Services\Auth;
interface AuthStrategyInterface {
    public function login(array $credentials): bool;
    public function logout(): void;
}