<?php

declare(strict_types=1);

namespace App\Tests;

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;

class TestAppFactory
{
    public static function getAppInstance(array $overrides = []): App
    {
        $containerBuilder = new ContainerBuilder();
        $definitions = require __DIR__ . '/../config/container.php';
        if (!empty($overrides)) {
            $definitions = array_merge($definitions, $overrides);
        }
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $routes = require __DIR__ . '/../config/routes.php';
        $routes($app);
        return $app;
    }
}
