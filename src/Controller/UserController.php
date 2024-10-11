<?php

namespace Routing\Controller;

use Routing\Service\UsersService;

class UserController extends AbstractController
{
    public function getUserConfigAction()
    {
        $data = $this->getData();
        $routerService = new UsersService();
        return ['status' => $routerService->getUserConfig($data)];
    }
}