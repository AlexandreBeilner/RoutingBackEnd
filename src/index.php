<?php

use Routing\Core\Router\Router;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();

try {
    $router->start();
} catch (Exception $exception) {
    echo json_encode([
        'status' => false,
        'message' => $exception->getMessage()
    ]);
}
