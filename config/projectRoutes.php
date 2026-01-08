<?php

use Controller\ProjectController;
use Controller\PingApiController;
use Core\Request;
use Core\Response;
use Core\Router;
use Repository\ProjectsRepository;

return function (Router $router, ProjectController $projectController, PingApiController $pingApiController, ProjectsRepository $projectsRepository) : void {
    // Routes API.
    $router->get('/api/ping', [$pingApiController, 'ping']);

    // Projet Routes
    $router->post('/api/projet/add', [$projectController, 'handleAddProject']);
    $router->get('/api/projet', function(Request $req, Response $res) use ($projectController, $projectsRepository) {
        $projectController->getProjects($req, $projectsRepository, $res);
    });
    $router->getRegex('#^/api/projet/nom/(.+)$#', function(Request $req, Response $res, array $m) use ($projectController) {
        $projectController->getByName($req, $res, (string)$m[1]);
    });
    $router->getRegex('#^/api/projet/id/(\d+)$#', function(Request $req, Response $res, array $m) use($projectController, $projectsRepository) {
        $projectController->getById($req, $projectsRepository, $res, (int)$m[1]);
    });
    $router->post('/api/projet/update', [$projectController, 'updateProject']);
    $router->post('/api/projet/delete', [$projectController, 'deleteProject']);
    $router->post('/api/projet/delete-nom', [$projectController, 'deleteProjectByName']);
};