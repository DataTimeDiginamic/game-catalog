<?php

use Controller\ProjectController;
use Controller\PingApiController;
use Core\Cors;
use Core\Database;
use Core\Response;
use Core\Session;
use Core\Request;
use Core\Router;
use Repository\ProjectsRepository;

session_start();
require __DIR__ . '/../autoload.php';
$registerProjectRoutes = require __DIR__ . '/../config/projectRoutes.php';
$config = require_once __DIR__ . '/../config/db.php';

Cors::Handle();
//echo "Hello World!";

$response = new Response();
$projectRepository = new ProjectsRepository(Database::makePdo($config['db']));
$session = new Session();
$request = new Request();
$router = new Router();

$projectController = new ProjectController($response, $projectRepository, $session, $request);
$pingApiController = new PingApiController();

$registerProjectRoutes($router, $projectController, $pingApiController, $projectRepository);
$router->dispatch($request, $response);

//<form method="post" action="project/add">
//    <div class="field">
//        <label for="nom">Nom Projet</label>
//        <label>
//            <input type="text" name="nom">
//        </label>
//    </div>
//    <button type="submit">Ajouter Projet</button>
//</form>