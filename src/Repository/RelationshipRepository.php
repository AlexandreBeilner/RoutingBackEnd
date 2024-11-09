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

    public function createRelation($driverID, $riderID, $routeID, $latitude, $longitude): bool | array
    {
        return $this->db->insert(['driverid' => $driverID, 'riderid' => $riderID, 'idroute' => $routeID, 'latitude' => $latitude, 'longitude' => $longitude]);
    }

    public function getRelationshipsByRouteAndDriverID($driverID, $routeID): array
    {
        $columns = 'relationship.idrelationship, relationship.driverid, relationship.riderid, relationship.amount, relationship.idroute,
        relationship.latitude, relationship.longitude,
        users.name as riderName, users.surname as riderSurname, users.phone as riderPhone, users.userimage as riderImage';

        $where = [
            ['column' => 'relationship.driverid', 'operator' => '=', 'value' => $driverID],
            ['column' => 'relationship.idroute', 'operator' => '=', 'value' => $routeID],
            ['column' => 'relationship.isrunning', 'operator' => '=', 'value' => 'true'],
        ];

        $join = [
            ['type' => "JOIN", 'table' => 'users', 'V1' => 'relationship.riderid', 'operator' => '=', 'V2' => 'users.iduser']
        ];

        return $this->db->select($columns, $where, $join);
    }

    public function setRunningStatus($where, $status): bool
    {
        $fields = ['isrunning'];
        $values = [$status];
        return $this->db->update($fields, $values, $where);
    }

    public function getRunningStatus($where): bool | array
    {
        $columns = 'isrunning';
        return $this->db->select($columns, $where);
    }

    public function getRelationship($where)
    {
        $columns = '*';
        return $this->db->select($columns, $where);
    }
}