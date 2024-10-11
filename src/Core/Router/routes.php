<?php
return [
    'facdrive' => [
        'Routing\Controller\RouterController' => [
            'GET' => [
                'getUserRoutesAction',
                'getRoutePointsAction',
                'getNearbyRoutesAction',
                'getRouteByRouteidAction'
            ],
            'POST' => [
                'saveRouteAction'
            ],
            'PUT' => [

            ],
            'DELETE' => [

            ]
        ],
        'Routing\Controller\UserController' => [
            'GET' => [
                'getUserConfigAction'
            ]
        ]
    ]
];
