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
    $router->post('/projet/add', [$projectController, 'handleAddProject']);
    $router->get('/projet', function(Request $req, Response $res) use ($projectController, $projectsRepository) {
        $projectController->getProjects($req, $projectsRepository, $res);
    });
};