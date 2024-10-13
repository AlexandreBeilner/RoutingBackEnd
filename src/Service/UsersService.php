<?php

namespace Routing\Service;

use Routing\Repository\UsersRepository;

class UsersService
{
    private UsersRepository $usersRepository;
    private GeocoderService $geocoderService;

    public function __construct()
    {
        $this->usersRepository = new UsersRepository();
        $this->geocoderService = new GeocoderService();
    }

    public function getUserConfig($data): array
    {
        $columns = 'iduser, name, surname, isdriver, userimage';
        $where = [];
        $where[] = ['column' => 'iduser', 'operator' => '=', 'value' => $data['iduser'] ?? 0];
        return $this->usersRepository->getUserData($columns, $where);
    }

    public function getCoordinatesByUserAddress($data): array | bool
    {
        $userID = $data['iduser'];
        return $this->geocoderService->getCoordinatesByAddress($userID);
    }

}