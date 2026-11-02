<?php

use Controller\SuiviController;
use Core\Router;

return function (Router $router, SuiviController $suiviController) {
    $router->get('/api/historique', [$suiviController, 'historique']);
    $router->get('/api/planning', [$suiviController, 'planning']);
    $router->get('/api/salaires', [$suiviController, 'salaires']);
};
