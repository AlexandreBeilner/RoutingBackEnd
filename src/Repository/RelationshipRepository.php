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

    public function getRelationshipsByRouteAndDriverID($driverID, $routeID): array
    {
        $columns = 'relationship.idrelationship, relationship.driverid, relationship.riderid, relationship.amount, relationship.idroute,
        users.name as riderName, users.surname as riderSurname, users.phone as riderPhone, users.userimage as riderImage';

        $where = [
            ['column' => 'relationship.driverid', 'operator' => '=', 'value' => $driverID],
            ['column' => 'relationship.idroute', 'operator' => '=', 'value' => $routeID]
        ];

        $join = [
            ['type' => "JOIN", 'table' => 'users', 'V1' => 'relationship.riderid', 'operator' => '=', 'V2' => 'users.iduser']
        ];

        return $this->db->select($columns, $where, $join);
    }
}