<?php

use Controller\ClientController;
use Controller\PingApiController;
use Core\Request;
use Core\Response;
use Core\Router;
use Repository\ClientsRepository;

return function (Router $router, ClientController $clientController, PingApiController $pingApiController, ClientsRepository $clientsRepository) : void {
    // Routes API.
    $router->get('/api/ping', [$pingApiController, 'ping']);

    // Client Routes
    $router->post('/api/client/add', [$clientController, 'handleAddClient']);
    $router->get('/api/client', function(Request $req, Response $res) use ($clientController, $clientsRepository) {
        $clientController->getClients($req, $clientsRepository, $res);
    });
    $router->getRegex('#^/api/client/nom/(.+)$#', function(Request $req, Response $res, array $m) use ($clientController) {
        $clientController->getByName($req, $res, (string)$m[1]);
    });
    $router->getRegex('#^/api/client/id/(\d+)$#', function(Request $req, Response $res, array $m) use($clientController, $clientsRepository) {
        $clientController->getById($req, $clientsRepository, $res, (int)$m[1]);
    });
    $router->post('/api/client/update', [$clientController, 'updateClient']);
    $router->post('/api/client/delete', [$clientController, 'deleteClient']);
    $router->post('/api/client/delete-nom', [$clientController, 'deleteClientByName']);
};