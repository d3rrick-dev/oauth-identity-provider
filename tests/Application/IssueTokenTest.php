<?php

namespace App\Tests\Application;

use App\Auth\Application\IssueToken;
use App\Auth\Domain\Client;
use App\Auth\Domain\ClientRepository;
use App\Auth\Infrastructure\JwtGenerator;
use Exception;
use PHPUnit\Framework\TestCase;

class IssueTokenTest extends TestCase
{
    public function testItThrowsExceptionOnInvalidSecret(): void
    {
        $repo = $this->createMock(ClientRepository::class);
        $generator = $this->createMock(JwtGenerator::class);

        $client = new Client('app', password_hash('correct', PASSWORD_BCRYPT), 'Name', []);
        $repo->method('findByIdentifier')->willReturn($client);

        $service = new IssueToken($repo, $generator);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid client credentials');

        $service->execute('app', 'wrong-password');
    }
}