<?php

namespace Routing\Service;

use Routing\Exceptions\RouteExceptions;
use Routing\Helper\RouterHelper;
use Routing\Repository\RelationshipRepository;
use Routing\Repository\RoutePointsRepository;
use Routing\Repository\RouteRepository;

class RouterService
{
    private RouteRepository $routeRepository;
    private RoutePointsRepository $routePointsRepository;
    private GeocoderService $geocoderService;
    private RelationshipRepository $relationshipRepository;

    public function __construct()
    {
        $this->routePointsRepository = new RoutePointsRepository();
        $this->relationshipRepository = new RelationshipRepository();
        $this->routeRepository = new RouteRepository();
        $this->geocoderService = new GeocoderService();
    }

    /**
     * @throws RouteExceptions
     */
    public function saveRoute($data): bool
    {
        $userID = $data['userID'];
        $routeName = $data['routeName'];
        $response = $this->routeRepository->saveNewRoute(['iduser' => $userID, 'routename' => $routeName]);
        if (!$response) {
            throw new RouteExceptions(1);
        }

        return $this->saveRoutePoints($data['route'], $response);
    }

    /**
     * @throws RouteExceptions
     */
    public function saveRoutePoints($data, $routeID): bool
    {
        foreach ($data as $item) {
            $values = ['longitude' => $item['lng'], 'latitude' => $item['lat'], 'idroute' => $routeID];
            $response = $this->routePointsRepository->saveRoutePoints($values);
            if (!$response['status']) {
                throw new RouteExceptions(1);
            }
        }

        return true;
    }

    public function getUserRoutes($data): array
    {
        $columns = 'idroute, iduser, routename';
        $where = [];
        $where[] = ['column' => 'iduser', 'operator' => '=', 'value' => $data['iduser'] ?? 0];
        $userRoutes = $this->routeRepository->getRoutes($columns, $where);
        if (count($userRoutes) === 0) {
            return [];
        }
        foreach ($userRoutes as $key => $route) {
            $userRoutes[$key]['routePoints'] = $this->getRoutePoints(['idroute' => $route['idroute']]);
        }
        return $userRoutes;
    }

    public function getRoutePoints($data): array
    {
        $columns = 'idroutepoints, idroute, longitude, latitude';
        $where = [];
        $where[] = ['column' => 'idroute', 'operator' => '=', 'value' => $data['idroute'] ?? 0];
        return $this->routePointsRepository->getRoutePoints($columns, $where);
    }

    public function getNearbyRoutesAndUserData($data): array
    {
        $coordinates = $this->getCoordinate($data);
        $routes = $this->getNearbyRoutes($data, $coordinates);
        $classDaysService = new ClassDaysService();
        foreach ($routes as $key => $route) {
            $routes[$key]['user'] = $classDaysService->getClassDaysByUserID($route['iduser']);
        }

        return (new RouterHelper())->formatNearbyRoutesArray($routes);
    }

    private function getCoordinate($data): array | bool
    {
        if ($data['latitude'] != 0 && $data['longitude'] != 0) {
            return ['latitude' => $data['latitude'], 'longitude' => $data['longitude']];
        }

        return $this->geocoderService->getCoordinatesByAddress($data['userID']);
    }

    private function getNearbyRoutes($data, $coordinates): array
    {
        $userID = $data['userID'] ?? 0;
        $maxDistance = $data['distance'];
        $distance = $this->sqlToCalculateDistanceInMeters($coordinates);

        $subquery = "(SELECT route.idroute, MIN({$distance}) AS min_distance
                  FROM route
                  JOIN routepoints ON route.idroute = routepoints.idroute
                  WHERE route.iduser != {$userID}
                  GROUP BY route.idroute) AS subq";

        $columns = "route.idroute, route.iduser, route.routename,
                routepoints.idroutepoints,
                CAST(routepoints.latitude AS numeric) AS latitude,
                CAST(routepoints.longitude AS numeric) AS longitude,
                {$distance} AS distance";

        $join = [
            ['type' => "JOIN", 'table' => 'routepoints', 'V1' => 'route.idroute', 'operator' => '=', 'V2' => 'routepoints.idroute'],
            ['type' => "JOIN", 'table' => $subquery, 'V1' => 'route.idroute', 'operator' => '=', 'V2' => 'subq.idroute'],
        ];

        $where = [
            ['column' => 'route.iduser', 'operator' => '!=', 'value' => $userID],
            ['column' => "{$distance}", 'operator' => '=', 'value' => 'subq.min_distance']
        ];

        $groupBy = "route.idroute, route.iduser, route.routename, routepoints.idroutepoints, routepoints.latitude, routepoints.longitude";
        $having = "{$distance} < {$maxDistance}";
        $orderBy = 'distance ASC';
        $limit = '10';

        return $this->routeRepository->getRoutes($columns, $where, $join, $having, $orderBy, $limit, $groupBy);
    }

    private function sqlToCalculateDistanceInMeters($coordinates): string
    {
        return "(
                   6371000 * acos(
                           cos(radians({$coordinates['latitude']})) * cos(radians(CAST(routepoints.latitude AS numeric))) *
                           cos(radians(CAST(routepoints.longitude AS numeric)) - radians({$coordinates['longitude']})) +
                           sin(radians({$coordinates['latitude']})) * sin(radians(CAST(routepoints.latitude AS numeric)))
                             )
               )";
    }

    public function getCompleteRouteByRouteID($data): array
    {
        $routeID = $data['routeID'];
        $columns = "route.idroute, route.iduser, route.routename, routepoints.idroutepoints, routepoints.longitude, routepoints.latitude";
        $join = [
            ['type' => "JOIN", 'table' => 'routepoints', 'V1' => 'route.idroute', 'operator' => '=', 'V2' => 'routepoints.idroute'],
        ];

        $where = [
            ['column' => 'route.idroute', 'operator' => '=', 'value' => $routeID],
        ];

        $completeRoute = $this->routeRepository->getRoutes($columns, $where, $join);
        return (new RouterHelper())->formatCompleteRouteArray($completeRoute);
    }

    public function deleteRoute($data): bool
    {
        $routeId = $data['routeID'];
        $respRoutePoints = $this->routePointsRepository->deletePoints($routeId);
        $respRoute = $this->routeRepository->deleteRoute($routeId);
        return $respRoute && $respRoutePoints;
    }

    public function getRidersByRoute($data): array
    {
        $driverID = $data['driverID'];
        $route = $data['routeID'];

        return $this->relationshipRepository->getRelationshipsByRouteAndDriverID($driverID, $route);
    }

    public function setRunningStatus($data): bool
    {
        $route = $data['routeID'];
        $driverID = $data['driverID'];
        $status = $data['status'];
        return $this->relationshipRepository->setRunningStatus($route, $driverID, $status);
    }

    public function getRunningStatus($data): bool | array
    {
        $where = [];
        if (isset($data['driverID'])) {
            $where[] = ['column' => 'relationship.driverid', 'operator' => '=', 'value' => $data['driverID']];
        } else if ($data['riderID']) {
            $where[] = ['column' => 'relationship.riderid', 'operator' => '=', 'value' => $data['riderID']];
        }
        $where[] = ['column' => 'relationship.idroute', 'operator' => '=', 'value' => $data['routeID']];

        return $this->relationshipRepository->getRunningStatus($where);
    }

}
