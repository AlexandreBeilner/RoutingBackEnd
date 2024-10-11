<?php

namespace Routing\Repository;

use Routing\Core\Database\PostgresDB;

class UsersRepository extends AbstractRepository
{
    const string TABLE =  'users';

    private PostgresDB $db;

    public function __construct()
    {
        parent::__construct(self::TABLE);
        $this->db = $this->getDb();
    }

    public function getUserData($columns, $where)
    {
        return $this->db->select($columns, $where);
    }

}