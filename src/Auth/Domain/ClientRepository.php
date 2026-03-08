<?php

declare(strict_types=1);

namespace App\Auth\Domain;

interface ClientRepository
{
    public function findByIdentifier(string $identifier): ?Client;
    public function save(Client $client): void;
}
