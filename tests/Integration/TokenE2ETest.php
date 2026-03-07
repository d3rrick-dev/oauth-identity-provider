<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Auth\Domain\Client;
use App\Tests\BaseTestCase;
use Slim\Exception\HttpUnauthorizedException;

class TokenE2ETest extends BaseTestCase
{
    public function testTokenEndpointReturnsValidJwt(): void
    {
        $clientId = 'mobile-app';
        $plainSecret = 'p@ssword123';

        $testClient = new Client(
            $clientId,
            password_hash($plainSecret, PASSWORD_BCRYPT),
            'Test Mobile App',
            ['messages.read']
        );

        $this->clientRepository->save($testClient);
        $savedClient = $this->clientRepository->findByIdentifier($clientId);

        $request = $this->createRequest('POST', '/auth/token', [
            'client_id'     => $savedClient->getIdentifier(),
            'client_secret' => $plainSecret
        ]);

        $response = $this->app->handle($request);
        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode(), "Response body: " . $response->getBody());
        $this->assertArrayHasKey('access_token', $payload);
        $this->assertEquals('Bearer', $payload['token_type']);

        $tokenParts = explode('.', $payload['access_token']);
        $this->assertCount(3, $tokenParts, 'JWT should have 3 parts (Header.Payload.Signature)');

        $claims = json_decode(base64_decode($tokenParts[1]), true);
        $this->assertEquals($savedClient->getIdentifier(), $claims['client_id']);
        $this->assertContains('default', $claims['scopes']);
    }

    public function testTokenEndpointReturns401ForInvalidSecret(): void
    {
        $clientId = 'secure-app';
        $correctSecret = 'real-password-123';

        $testClient = new Client(
            $clientId,
            password_hash($correctSecret, PASSWORD_BCRYPT),
            'Secure App',
            ['messages.read']
        );
        $this->clientRepository->save($testClient);
        $request = $this->createRequest('POST', '/auth/token', [
            'client_id'     => $clientId,
            'client_secret' => '34'
        ]);

        $this->expectException(HttpUnauthorizedException::class);
        $this->expectExceptionMessage('Invalid client credentials');
        $this->app->handle($request);
    }
}