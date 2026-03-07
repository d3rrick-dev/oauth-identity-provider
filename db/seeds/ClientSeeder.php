<?php

use Phinx\Seed\AbstractSeed;

class ClientSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'client_id'     => 'web-app',
                'client_secret' => password_hash('super-secret-123', PASSWORD_BCRYPT),
                'client_name'   => 'web application',
                'scopes'        => json_encode(['messages.read', 'messages.write']),
                'created_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        $table = $this->table('oauth_clients');
        $table->insert($data)->saveData();
    }
}