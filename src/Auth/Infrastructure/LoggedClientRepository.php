<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use App\Auth\Domain\Client;
use App\Auth\Domain\ClientRepository;
use Doctrine\DBAL\Connection;
use Exception;

class LoggedClientRepository implements ClientRepository
{
    public function __construct(
        private Connection $db,
    ) {}

    public function findByIdentifier(string $identifier): ?Client
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();

            $result = $queryBuilder
                ->select('client_id', 'client_secret', 'client_name', 'scopes')
                ->from('oauth_clients')
                ->where('client_id = :id')
                ->andWhere('is_active = 1')
                ->setParameter('id', $identifier)
                ->executeQuery()
                ->fetchAssociative();

            if (!$result) {
                return null;
            }

            return new Client(
                $result['client_id'],
                $result['client_secret'],
                $result['client_name'],
                json_decode($result['scopes'] ?? '[]', true),
            );
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function save(Client $client): void
    {
        //TODO: needs more work, either decide to update existing details oer use another api
        $this->db->insert('oauth_clients', [
            'client_id' => $client->getIdentifier(),
            'client_secret' => $client->getHashedSecret(),
            'client_name' => $client->getName(),
            'scopes' => json_encode($client->getScopes()),
        ]);
    }
}
