<?php

use Controller\AuthController;
use Core\Router;

return function (Router $router, AuthController $authController) : void {
    $router->post('/api/login', [$authController, 'login']);
    $router->post('/api/logout', [$authController, 'logout']);
    $router->get('/api/me', [$authController, 'me']);
};
