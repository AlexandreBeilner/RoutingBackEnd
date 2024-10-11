<?php

namespace Routing\Repository;

use Routing\Core\Database\PostgresDB;

class ClassdaysRepository extends AbstractRepository
{
    const TABLE =  'classdays';
    private PostgresDB $db;

    public function __construct()
    {
        parent::__construct(self::TABLE);
        $this->db = $this->getDb();
    }

    public function getClassDaysByUserId($userID): array
    {
        $columns = "classdays.iduser, classdays.monday, classdays.tuesday, classdays.wednesday, 
        classdays.thursday, classdays.friday, classdays.saturday, users.name";

        $where = [
            ['column' => 'classdays.iduser', 'operator' => '=', 'value' => $userID]
        ];

        $join = [
            ['type' => "JOIN", 'table' => 'users', 'V1' => 'classdays.iduser', 'operator' => '=', 'V2' => 'users.iduser']
        ];

        return $this->db->select($columns, $where, $join);
    }
}