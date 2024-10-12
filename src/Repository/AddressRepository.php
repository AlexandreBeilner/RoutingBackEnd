<?php

namespace Routing\Repository;

use Routing\Core\Database\PostgresDB;
use Routing\Repository\AbstractRepository;

class AddressRepository extends AbstractRepository
{
    const TABLE =  'address';
    private PostgresDB $db;

    public function __construct()
    {
        parent::__construct(self::TABLE);
        $this->db = $this->getDb();
    }

    public function getAddressByUserID($userID): array
    {
        $columns = 'address.iduser, address.zipcode, address.street, address.neighborhood, address.city, address.number, address.state';
        $where = [
            ['column' => 'address.iduser', 'operator' => '=', 'value' => $userID]
        ];

        return $this->db->select($columns, $where);
    }

}