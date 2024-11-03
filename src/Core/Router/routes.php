<?php
return [
    'facdrive' => [
        'Routing\Controller\RouterController' => [
            'GET' => [
                'getUserRoutesAction',
                'getRoutePointsAction',
                'getNearbyRoutesAction',
                'getRouteByRouteidAction',
                'getRidersByRouteAction'
            ],
            'POST' => [
                'saveRouteAction'
            ],
            'PUT' => [

            ],
            'DELETE' => [
                'deleteRouteAction'
            ]
        ],
        'Routing\Controller\UserController' => [
            'GET' => [
                'getUserConfigAction',
                'getCoordinatesByUserAddressAction'
            ],
            'POST' => [
                'createRelationshipAction'
            ]
        ]
    ]
];
