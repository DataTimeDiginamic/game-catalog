<?php

use Controller\ProjectController;
use Controller\ClientController;
use Controller\AbsenceController;
use Controller\PingApiController;
use Core\Cors;
use Core\Database;
use Core\Response;
use Core\Session;
use Core\Request;
use Core\Router;

use Repository\ProjectsRepository;
use Repository\ClientsRepository;

session_start();
require __DIR__ . '/../autoload.php';
$registerProjectRoutes = require __DIR__ . '/../config/projectRoutes.php';
$registerClientsRoutes = require __DIR__ . '/../config/clientRoutes.php';
$registerAbsenceRoutes = require __DIR__ . '/../config/absenceRoutes.php';
$config = require_once __DIR__ . '/../config/db.php';

Cors::Handle();

$response = new Response();
$projectRepository = new ProjectsRepository(Database::makePdo($config['db']));
$clientRepository = new ClientsRepository(Database::makePdo($config['db']));
$session = new Session();
$request = new Request();
$router = new Router();

$projectController = new ProjectController($response, $projectRepository, $session, $request);
$clientController = new ClientController($response, $clientRepository, $session, $request);
$absenceController = new AbsenceController(Database::makePdo($config['db']));
$pingApiController = new PingApiController();

$registerProjectRoutes($router, $projectController, $pingApiController, $projectRepository);
$registerClientsRoutes($router, $clientController, $pingApiController, $clientRepository);
$registerAbsenceRoutes($router, $pingApiController, $absenceController);
$router->dispatch($request, $response);