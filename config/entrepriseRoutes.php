<?php

use Controller\EntrepriseController;
use Core\Request;
use Core\Response;
use Core\Router;

return function (Router $router, EntrepriseController $entrepriseController) {
    $router->getRegex('#^/api/entreprise/(\d+)$#', function (Request $req, Response $res, array $m) use ($entrepriseController) {
        $entrepriseController->rechercher($m[1]);
    });
};
