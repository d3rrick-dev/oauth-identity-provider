<?php

declare(strict_types=1);

namespace App\Tests;

use App\Auth\Domain\ClientRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class BaseTestCase extends TestCase
{
    protected Connection $db;
    protected ContainerInterface $container;
    protected App $app;
    protected ClientRepository $clientRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = TestAppFactory::getAppInstance();
        $this->container = $this->app->getContainer();
        $routes = require __DIR__ . '/../config/routes.php';
        $routes($this->app);
        $this->setupDatabase();

        $this->clientRepository = $this->container->get(ClientRepository::class);
    }

    private function setupDatabase(): void
    {
        $this->db = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);

        $this->container->set(Connection::class, $this->db);
        $this->migrateDatabase();
    }

    private function migrateDatabase(): void
    {
        $configData = require __DIR__ . '/../phinx.php';
        $configData['environments']['testing'] = [
            'adapter' => 'sqlite',
            'connection' => $this->db->getNativeConnection(),
        ];

        $manager = new Manager(
            new Config($configData),
            new ArrayInput([]),
            new NullOutput()
        );
        $manager->migrate('testing');
        $manager->seed('testing');
    }

    protected function createRequest(
        string $method,
        string $path,
        array $params = []
    ): ServerRequestInterface {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest($method, $path);

        if ($method === 'POST') {
            return $request->withParsedBody($params)
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        }

        return $request;
    }

    protected function tearDown(): void
    {
        if (isset($this->db) && $this->db->isConnected()) {
            $this->db->close();
        }
        parent::tearDown();
    }
}