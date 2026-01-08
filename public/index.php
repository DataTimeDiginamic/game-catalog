<?php

use Controller\ProjectController;
use Controller\ClientController;
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
$pingApiController = new PingApiController();

$registerProjectRoutes($router, $projectController, $pingApiController, $projectRepository);
$registerClientsRoutes($router, $clientController, $pingApiController, $clientRepository);
$router->dispatch($request, $response);

//<!--//<form method="post" action="api/projet/delete-nom">-->
//<!--//    <div class="field">-->
//<!--//        <label for="id">ID Projet</label>-->
//<!--//        <label>-->
//<!--//            <input type="number" name="id">-->
//<!--//        </label>-->
//<!--//    </div>-->
//<!--//    <div class="field">-->
//<!--//        <label for="nom">Nom Projet</label>-->
//<!--//        <label>-->
//<!--//            <input type="text" name="nom">-->
//<!--//        </label>-->
//<!--//    </div>-->
//<!--//    <button type="submit">Supprimer Projet</button>-->
//<!--//</form>-->
//
//<!--<form method="post" action="api/client/delete-nom">-->
//<!--        <div class="field">-->
//<!--            <label for="id">ID Client</label>-->
//<!--           <label>-->
//<!--                <input type="number" name="id">-->
//<!--           </label>-->
//<!--        </div>-->
//<!--        <div class="field">-->
//<!--    <div class="field">-->
//<!--        <label for="nom">Nom Client</label>-->
//<!--        <label>-->
//<!--            <input type="text" name="nom">-->
//<!--        </label>-->
//<!--    </div>-->
//<!--    <button type="submit">Supprimer Client</button>-->
//<!--</form>-->