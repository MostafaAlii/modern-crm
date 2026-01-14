<?php
namespace App\Services\Auth\Strategy;
use App\Models\Client;
class ClientAuthStrategy extends BaseAuthStrategy {
    public function __construct() {
        parent::__construct('client', Client::class);
    }
}