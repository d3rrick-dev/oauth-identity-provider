<?php

declare(strict_types=1);

return
    [
        'paths' => [
            'migrations' => __DIR__ . '/db/migrations',
            'seeds' => __DIR__ . '/db/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'development' => [
                'adapter' => 'sqlite',
                'name' => __DIR__ . '/var/storage/database',
                'suffix' => '.sqlite',
            ]
        ],
        'version_order' => 'creation'
    ];