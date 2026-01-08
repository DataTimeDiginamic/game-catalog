<?php

use Controller\AppController;
use Controller\PingApiController;
use Controller\TacheApiController;
use Core\Cors;
use Core\Database;
use Core\Request;
use Core\Response;
use Core\Router;
use Core\Session;
use Repository\TacheRepository;

session_start();
require __DIR__ . '/../autoload.php';

$config = require __DIR__ . '/../config/db.php';

Cors::handle();

// Core services
$response = new Response();
$session = new Session();
$request = new Request();
$router = new Router();

// Connexion PDO
$pdo = Database::makePdo($config['db']);

// Dépendances
$appController = new AppController($response, $session, $request);
$pingApiController = new PingApiController();

// Repository pour Tache
$tacheRepository = new TacheRepository($pdo);

// Controller Tache avec injection des dépendances
$tacheApiController = new TacheApiController(
    $response,
    $tacheRepository,
    $request
);

// Chargement des routes
$registerRoutes = require __DIR__ . '/../config/routes.php';
$registerRoutes($router, $appController, $pingApiController, $tacheApiController);

// Dispatch
$router->dispatch($request, $response);
