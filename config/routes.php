<?php

use App\Auth\Infrastructure\Middleware\JwtAuthMiddleware;
use App\Auth\Infrastructure\TokenController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (Slim\App $app) {
    $app->post('/auth/token', TokenController::class);

    $app->get('/auth/validate', function (Request $request, Response $response) {
        $claims = $request->getAttribute('token_claims');

        $data = [
            'status' => 'Token is valid',
            'client_id' => $claims['client_id'],
            'scopes' => $claims['scopes'],
            'expires_at' => date('Y-m-d H:i:s', $claims['exp']->getTimestamp()),
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(JwtAuthMiddleware::class);
};
