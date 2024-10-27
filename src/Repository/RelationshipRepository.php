<?php

namespace Routing\Repository;

use Routing\Core\Database\PostgresDB;
use Routing\Repository\AbstractRepository;

class RelationshipRepository extends AbstractRepository
{
    const TABLE =  'relationship';
    private PostgresDB $db;

    public function __construct()
    {
        parent::__construct(self::TABLE);
        $this->db = $this->getDb();
    }

    public function createRelation($driverID, $riderID, $routeID): bool | array
    {
        return $this->db->insert(['driverid' => $driverID, 'riderid' => $riderID, 'idroute' => $routeID]);
    }
}