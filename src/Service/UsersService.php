<?php

namespace Routing\Service;

use Routing\Repository\UsersRepository;

class UsersService
{
    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new UsersRepository();
    }

    public function getUserConfig($data): array
    {
        $columns = 'iduser, name, isdriver, isdriver';
        $where = [];
        $where[] = ['column' => 'iduser', 'operator' => '=', 'value' => $data['iduser'] ?? 0];
        return $this->usersRepository->getUserData($columns, $where);
    }

}