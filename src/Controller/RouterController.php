<?php

namespace Routing\Controller;

use Routing\Exceptions\RouteExceptions;
use Routing\Service\RouterService;

class RouterController extends AbstractController {

    /**
     * @throws RouteExceptions
     */
    public function saveRouteAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => $routerService->saveRoute($data)];
    }

    public function getUserRoutesAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => 'true', 'response' => $routerService->getUserRoutes($data)];
    }

    public function  getRoutePointsAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => 'true', 'response' => $routerService->getRoutePoints($data)];
    }

    public function getNearbyRoutesAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => 'true', 'response' => $routerService->getNearbyRoutesAndUserData($data)];
    }

    public function getRouteByRouteidAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => 'true', 'response' => $routerService->getCompleteRouteByRouteID($data)];
    }

    public function deleteRouteAction(): array
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => $routerService->deleteRoute($data)];
    }

    public function getRidersByRouteAction()
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => true, 'response' => $routerService->getRidersByRoute($data)];
    }

    public function setRunningStatusAction()
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => true, 'response' => $routerService->setRunningStatus($data)];
    }

    public function getRunningStatusAction()
    {
        $data = $this->getData();
        $routerService = new RouterService();
        return ['status' => true, 'response' => $routerService->getRunningStatus($data)];
    }

}
