<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use Lcobucci\JWT\Configuration;
use DateTimeImmutable;

readonly class JwtGenerator
{
    public function __construct(
        private Configuration $jwtConfig,
    ) {}

    public function generate(string $clientId, array $scopes, int $ttl): string
    {
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify("+{$ttl} seconds");
        return $this->jwtConfig->builder()
            // iss: Who issued the token -> this auth server
            ->issuedBy('https://auth.this-app.com')
            // aud: Who is the token for (Your s Server/API)
            ->permittedFor('https://api.my-app.com')
            // jti: Unique ID for the token to prevents replay attacks
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            // exp: 1hr
            ->expiresAt($expiresAt)
            // Custom Claims
            ->withClaim('client_id', $clientId)
            ->withClaim('scopes', $scopes)
            ->getToken(
                $this->jwtConfig->signer(),
                $this->jwtConfig->signingKey(),
            )
            ->toString();
    }
}
