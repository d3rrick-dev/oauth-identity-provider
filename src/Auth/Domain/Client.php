<?php

declare(strict_types=1);

namespace App\Auth\Domain;

class Client
{
    public function __construct(
        private string $identifier,
        private string $hashedSecret,
        private string $name,
        private array $scopes = ['basic'],
    ) {}

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function verifySecret(string $plainSecret): bool
    {
        return password_verify($plainSecret, $this->hashedSecret);
    }

    public function getHashedSecret()
    {
        return $this->hashedSecret;
    }
}
