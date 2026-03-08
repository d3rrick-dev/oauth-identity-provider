<?php

use App\Auth\Application\IssueToken;
use App\Auth\Domain\ClientRepository;
use App\Auth\Infrastructure\JwtGenerator;
use App\Auth\Infrastructure\JwtValidator;
use App\Auth\Infrastructure\LoggedClientRepository;
use App\Auth\Infrastructure\Middleware\JwtAuthMiddleware;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

return [
    Configuration::class => function () {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(__DIR__ . '/../var/keys/private.pem'),
            InMemory::file(__DIR__ . '/../var/keys/public.pem'),
        );
    },

    Connection::class => function () {
        $dir = realpath(__DIR__ . '/../var/storage');
        if (!$dir) {
            throw new RuntimeException("Storage directory not found at var/storage");
        }
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $dir . '/database.sqlite',
        ]);
    },

    ClientRepository::class => DI\get(LoggedClientRepository::class),

    JwtGenerator::class => function ($c) {
        return new JwtGenerator($c->get(Configuration::class));
    },

    JwtValidator::class => function ($c) {
        return new JwtValidator($c->get(Configuration::class));
    },
    'jwt.ttl' => (int) ($_ENV['JWT_TTL'] ?? 30), //30 secs
    IssueToken::class => function ($c) {
        return new IssueToken(
            $c->get(ClientRepository::class),
            $c->get(JwtGenerator::class),
            $c->get('jwt.ttl'),
        );
    },

    JwtAuthMiddleware::class => DI\autowire(),
];
