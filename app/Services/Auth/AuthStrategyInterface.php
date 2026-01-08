<?php
namespace App\Services\Auth;
interface AuthStrategyInterface {
    public function attempt(array $credentials): mixed;
    public function loginUser(mixed $user): void;
    public function logout(): void;
}