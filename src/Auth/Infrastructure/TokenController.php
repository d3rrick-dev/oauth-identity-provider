<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use App\Auth\Application\IssueToken;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpUnauthorizedException;

readonly class TokenController
{
    public function __construct(
        private IssueToken $issueToken
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();
        $clientId = $params['client_id'] ?? '';
        $clientSecret = $params['client_secret'] ?? '';

        try {
            $jwt = $this->issueToken->execute($clientId, $clientSecret);

            $payload = [
                'access_token' => $jwt,
                'token_type'   => 'Bearer',
                'expires_in'   => 3600
            ];

            $response->getBody()->write(json_encode($payload));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (Exception $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }
    }
}