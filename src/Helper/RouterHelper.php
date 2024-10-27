<?php

namespace Routing\Helper;

class RouterHelper
{
    public function formatNearbyRoutesArray($routes): array
    {
        $results = [];
        foreach ($routes as $route) {
            $results[] = [
                'userID' => $route['iduser'],
                'userName' => $route['user'][0]['name'],
                'routeID' => $route['idroute'],
                'distance' => round($route['distance'], 1) . ' Metros',
                'nearbyPoint' => ['lat' => $route['latitude'], 'lng' => $route['longitude']],
                'classDays' => [
                    ['name' => 'Seg', 'isGoing' => $route['user'][0]['monday'] ?? false],
                    ['name' => 'Ter', 'isGoing' => $route['user'][0]['tuesday'] ?? false],
                    ['name' => 'Qua', 'isGoing' => $route['user'][0]['wednesday'] ?? false],
                    ['name' => 'Qui', 'isGoing' => $route['user'][0]['thursday'] ?? false],
                    ['name' => 'Sex', 'isGoing' => $route['user'][0]['friday'] ?? false],
                    ['name' => 'Sab', 'isGoing' => $route['user'][0]['saturday'] ?? false]
                ]
            ];
        }

        return $results;
    }

    public function formatCompleteRouteArray($route): array
    {
        $results = [
            'idroute' => $route[0]['idroute'],
            'iduser' => $route[0]['iduser'],
            'routename' => $route[0]['routename'],
        ];
        foreach ($route as $points) {
            $results['routePoints'][] = [
                'idroutepoints' => $points['idroutepoints'],
                'longitude' => $points['longitude'],
                'latitude' => $points['latitude']
            ];
        }

        usort($results['routePoints'], function ($a, $b) {
            return $a['idroutepoints'] <=> $b['idroutepoints'];
        });
        return $results;
    }
}