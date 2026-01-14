<?php
namespace App\Services\Auth\Result;
class AuthResult {
    public function __construct(
        public bool $success,
        public ?string $reason = null,
        public mixed $user = null,
        public ?string $token = null,       // JWT token
        public ?\DateTime $expires_at = null, // Expiration
    ) {}
}
