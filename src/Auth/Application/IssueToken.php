<?php

declare(strict_types=1);

namespace App\Auth\Application;

use App\Auth\Domain\ClientRepository;
use App\Auth\Infrastructure\JwtGenerator;
use Exception;

class IssueToken
{
    public function __construct(
        private ClientRepository $clients,
        private JwtGenerator $jwtGenerator,
        private int $tokenTtl,
    ) {}

    public function execute(string $clientId, string $clientSecret, array $requestedScopes = []): array
    {
        $client = $this->clients->findByIdentifier($clientId);

        if (!$client || !$client->verifySecret($clientSecret)) {
            throw new Exception("Unauthorized: Invalid client credentials.");
        }
        // only grant scopes the client is actually allowed to have
        $allowedScopes = array_intersect($requestedScopes, $client->getScopes());
        $finalScopes = empty($allowedScopes) ? ['default'] : $allowedScopes;

        $jwt = $this->jwtGenerator->generate($client->getIdentifier(), $finalScopes, $this->tokenTtl);
        return [
            'token' => $jwt,
            'expires_in' => $this->tokenTtl,
        ];
    }
}
