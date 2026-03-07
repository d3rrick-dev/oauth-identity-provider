<?php

use App\Auth\Application\IssueToken;
use App\Auth\Domain\ClientRepository;
use App\Auth\Infrastructure\JwtGenerator;
use App\Auth\Infrastructure\LoggedClientRepository;
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
            InMemory::file(__DIR__ . '/../var/keys/public.pem')
        );
    },
    ClientRepository::class => DI\get(LoggedClientRepository::class),
    Connection::class => function() {
        $dbPath = realpath(__DIR__ . '/../var/storage') . '/database.sqlite';
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $dbPath
        ]);
    },

    JwtGenerator::class => DI\autowire(),

    IssueToken::class => function ($container) {
        return new IssueToken(
            $container->get(ClientRepository::class),
            $container->get(JwtGenerator::class),
        );
    },
];