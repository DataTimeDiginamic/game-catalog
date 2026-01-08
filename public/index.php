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
$registerRoutes = require __DIR__ . '/../config/routes.php';
$config = require_once __DIR__ . '/../config/db.php';

Cors::Handle();
//echo "Hello World!";

$response = new Response();
$repository = new ProjectsRepository(Database::makePdo($config['db']));
$session = new Session();
$request = new Request();
$router = new Router();

$projectController = new ProjectController($response, $repository, $session, $request);
$pingApiController = new PingApiController();

$registerRoutes($router, $projectController, $pingApiController);
$router->dispatch($request, $response);

//<form method="post" action="/add">
//    <div class="field">
//        <label for="nom">Nom Projet</label>
//        <label>
//            <input type="text" name="nom">
//        </label>
//    </div>
//    <button type="submit">Ajouter Projet</button>
//</form>