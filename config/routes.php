<?php

use Controller\AppController;
use Controller\PingApiController;
use Controller\TacheApiController;
use Core\Router;
use Core\Request;
use Core\Response;

return function (
    Router $router,
    AppController $controller,
    PingApiController $pingApiController,
    TacheApiController $tacheApiController
) {
    // Page d'accueil
    $router->get('/', [$controller, 'home']);

    // Test API
    $router->get('/ping', [$pingApiController, 'ping']);

    // -------------------------
    // 🚀 ROUTES API TÂCHES
    // -------------------------

    // LISTE
    $router->get('/taches', [$tacheApiController, 'index']);

    // CREATE
    $router->post('/taches', [$tacheApiController, 'store']);

    // SHOW (regex)
    $router->getRegex('#^/taches/(\d+)$#', function (Request $req, Response $res, array $m) use ($tacheApiController) {
        $tacheApiController->show($req, $res, (int)$m[1]);
    });

    // UPDATE (POST)
    $router->post('/taches/update', [$tacheApiController, 'update']);

    // DELETE (POST)
    $router->post('/taches/delete', [$tacheApiController, 'delete']);


};
