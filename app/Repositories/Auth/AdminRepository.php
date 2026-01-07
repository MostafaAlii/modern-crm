<?php

namespace App\Repositories\Auth;

use App\Models\Admin;
use App\Repositories\Auth\Contracts\AuthRepositoryInterface;

class AdminRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email)
    {
        return Admin::where('email', $email)->first();
    }

    public function create(array $data)
    {
        return Admin::create($data);
    }
}