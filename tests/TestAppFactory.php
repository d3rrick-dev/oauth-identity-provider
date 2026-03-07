<?php

declare(strict_types=1);

namespace App\Tests;

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;

class TestAppFactory
{
    public static function getAppInstance(): App
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
        $container = $containerBuilder->build();

        AppFactory::setContainer($container);
        return AppFactory::create();
    }
}