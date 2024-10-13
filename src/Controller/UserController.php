<?php

namespace Routing\Controller;

use Routing\Service\UsersService;

class UserController extends AbstractController
{
    public function getUserConfigAction()
    {
        $data = $this->getData();
        $routerService = new UsersService();
        return ['status' => true, 'response' => $routerService->getUserConfig($data)];
    }

    public function getCoordinatesByUserAddressAction()
    {
        $data = $this->getData();
        $routerService = new UsersService();
        return ['status' => true, 'response' => $routerService->getCoordinatesByUserAddress($data)];
    }
}