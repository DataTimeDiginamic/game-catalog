<?php

use Controller\UtilisateurController;
use Core\Request;
use Core\Response;
use Core\Router;

return function (Router $router, UtilisateurController $utilisateurController) : void {
    $router->get('/api/utilisateurs', [$utilisateurController, 'index']);
    $router->post('/api/utilisateurs', [$utilisateurController, 'store']);

    $router->getRegex('#^/api/utilisateurs/(\d+)$#', function (Request $req, Response $res, array $m) use ($utilisateurController) {
        $utilisateurController->show((int) $m[1]);
    });
    $router->postRegex('#^/api/utilisateurs/(\d+)$#', function (Request $req, Response $res, array $m) use ($utilisateurController) {
        $utilisateurController->update((int) $m[1]);
    });
    $router->deleteRegex('#^/api/utilisateurs/(\d+)$#', function (Request $req, Response $res, array $m) use ($utilisateurController) {
        $utilisateurController->delete((int) $m[1]);
    });
};
