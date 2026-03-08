<?php


declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\BaseTestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class JwtExpiryTest extends BaseTestCase
{
    public function testTokenExpiresAfterOneSecond(): void
    {
        $request = new ServerRequestFactory()->createServerRequest('POST', '/auth/token')
            ->withParsedBody([
                'client_id' => 'web-app',
                'client_secret' => 'super-secret-123',
            ]);

        $response = $this->app->handle($request);
        $data = json_decode((string) $response->getBody(), true);

        $token = $data['access_token'];
        $this->assertEquals(1, $data['expires_in']);

        sleep(2);

        $protectedRequest = new ServerRequestFactory()->createServerRequest('GET', '/auth/validate')
            ->withHeader('Authorization', 'Bearer ' . $token);

        $protectedResponse = $this->app->handle($protectedRequest);
        $result = json_decode((string) $protectedResponse->getBody(), true);

        $this->assertEquals(401, $protectedResponse->getStatusCode());
        $this->assertEquals('Token expired', $result['message']);
    }
}
