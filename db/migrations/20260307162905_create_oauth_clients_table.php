<?php

use Phinx\Migration\AbstractMigration;

class CreateOauthClientsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('oauth_clients', ['id' => false, 'primary_key' => 'client_id']);
        $table->addColumn('client_id', 'string', ['limit' => 80])
            ->addColumn('client_secret', 'string', ['limit' => 255])
            ->addColumn('client_name', 'string', ['limit' => 100])
            ->addColumn('redirect_uri', 'string', ['limit' => 2000, 'null' => true])
            ->addColumn('grant_types', 'string', ['limit' => 80, 'default' => 'client_credentials'])
            ->addColumn('scopes', 'string', ['limit' => 4000, 'default' => 'basic'])
            ->addColumn('is_active', 'boolean', ['default' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
