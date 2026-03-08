<?php


declare(strict_types=1);

namespace App\Auth\Infrastructure\Middleware;

use App\Auth\Infrastructure\JwtValidator;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

readonly class JwtAuthMiddleware
{
    public function __construct(private JwtValidator $validator) {}

    public function __invoke(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $claims = $this->validator->validate($token);
            $request = $request->withAttribute('token_claims', $claims);

            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new SlimResponse();

            $payload = [
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
                'status' => 401,
            ];

            $response->getBody()->write(json_encode($payload));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }
}
