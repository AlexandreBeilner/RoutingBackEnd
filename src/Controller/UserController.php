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

    public function createRelationshipAction()
    {
        $data = $this->getData();
        $userService = new UsersService();
        return $userService->createRelationship($data);
    }

    public function getUserRelationshipsAction()
    {
        $data = $this->getData();
        $userService = new UsersService();
        return ['status' => true, 'response' => $userService->getRelationships($data)];
    }

    public function deleteRelationshipAction()
    {
        $data = $this->getData();
        $userService = new UsersService();
        return ['status' => true, 'response' => $userService->deleteRelationship($data)];
    }
}