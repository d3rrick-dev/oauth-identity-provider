<?php

use App\Auth\Infrastructure\TokenController;
use Slim\App;

return function (App $app) {
$app->post('/auth/token', TokenController::class);
};