<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use DateTimeImmutable;
use Exception;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

readonly class JwtValidator
{
    public function __construct(private Configuration $config) {}

    public function validate(string $jwt): array
    {
        $token = $this->config->parser()->parse($jwt);
        $constraints = [
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            new StrictValidAt(SystemClock::fromSystemTimezone()),
        ];
        if (!$this->config->validator()->validate($token, ...$constraints)) {
            if ($token->isExpired(new DateTimeImmutable())) {
                throw new Exception("Token expired");
            }

            throw new Exception("Invalid token signature or claims");
        }

        return $token->claims()->all();
    }
}
