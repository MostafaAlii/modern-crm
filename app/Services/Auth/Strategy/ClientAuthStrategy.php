<?php
namespace App\Services\Auth\Strategy;
use App\Models\Client;
class ClientAuthStrategy extends BaseAuthStrategy {
    public function __construct() {
        parent::__construct('client');
    }

    protected function model(): string {
        return Client::class;
    }
}
