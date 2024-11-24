<?php

namespace Routing\Service;

use Routing\Repository\RelationshipRepository;
use Routing\Repository\UsersRepository;

class UsersService
{
    private UsersRepository $usersRepository;
    private GeocoderService $geocoderService;
    private RelationshipRepository $relationShipRepository;

    public function __construct()
    {
        $this->usersRepository = new UsersRepository();
        $this->geocoderService = new GeocoderService();
        $this->relationShipRepository = new RelationshipRepository();
    }

    public function getUserConfig($data): array
    {
        $columns = 'iduser, name, surname, isdriver, userimage, phone';
        $where = [];
        $where[] = ['column' => 'iduser', 'operator' => '=', 'value' => $data['iduser'] ?? 0];
        return $this->usersRepository->getUserData($columns, $where);
    }

    public function getCoordinatesByUserAddress($data): array | bool
    {
        $userID = $data['iduser'];
        return $this->geocoderService->getCoordinatesByAddress($userID);
    }

    public function createRelationship($data): bool | array
    {
        $driverID = $data['driverID'];
        $riderID = $data['riderID'];
        $routeID = $data['routeID'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        return $this->relationShipRepository->createRelation($driverID, $riderID, $routeID, $latitude, $longitude);
    }

    public function getRelationships($data): array | bool
    {
        $where = [];
        if (isset($data['driverID'])) {
            $where[] = ['column' => 'relationship.driverid', 'operator' => '=', 'value' => $data['driverID']];
        } else if ($data['riderID']) {
            $where[] = ['column' => 'relationship.riderid', 'operator' => '=', 'value' => $data['riderID']];
        }
        return $this->relationShipRepository->getRelationship($where);
    }

    public function deleteRelationship($data): bool
    {
        $where = [];
        if (isset($data['driverID'])) {
            $where[] = ['column' => 'relationship.driverid', 'operator' => '=', 'value' => $data['driverID']];
        } else if ($data['riderID']) {
            $where[] = ['column' => 'relationship.riderid', 'operator' => '=', 'value' => $data['riderID']];
        }
        $where[] = ['column' => 'relationship.idroute', 'operator' => '=', 'value' => $data['routeID']];
        return $this->relationShipRepository->deleteRelationship($where);
    }

}