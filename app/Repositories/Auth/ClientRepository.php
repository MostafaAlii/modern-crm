<?php
namespace App\Repositories\Auth;
use App\Models\Client;
use App\Repositories\Auth\Contracts\AuthRepositoryInterface;
class ClientRepository implements AuthRepositoryInterface {
    public function findByEmail(string $email) {
        return Client::where('email', $email)->first();
    }

    public function create(array $data) {
        return Client::create($data);
    }
}