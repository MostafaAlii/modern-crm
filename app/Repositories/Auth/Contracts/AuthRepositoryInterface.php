<?php
namespace App\Repositories\Auth\Contracts;
interface AuthRepositoryInterface {
    public function findByEmail(string $email);
    public function create(array $data);
}