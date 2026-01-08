<?php

use Controller\PingApiController;
use Controller\AbsenceController;
use Core\Request;
use Core\Response;
use Core\Router;

return function (Router $router, PingApiController $pingApiController, AbsenceController $absenceController) {
    // Routes API.
    $router->get('/api/ping', [$pingApiController, 'ping']);

    // Routes CRUD Absences
    $router->get('/api/absences', [$absenceController, 'index']);
    $router->post('/api/absences', [$absenceController, 'store']);

    $router->getRegex('#^/api/absences/(\d+)$#', function (Request $req, Response $res, array $m) use ($absenceController) {
        $absenceController->show($req, $res, (int)$m[1]);
    });

    $router->putRegex('#^/api/absences/(\d+)$#', function (Request $req, Response $res, array $m) use ($absenceController) {
        $absenceController->update($req, $res, (int)$m[1]);
    });

    $router->deleteRegex('#^/api/absences/(\d+)$#', function (Request $req, Response $res, array $m) use ($absenceController) {
        $absenceController->delete($req, $res, (int)$m[1]);
    });

    $router->getRegex('#^/api/salaries/(\d+)/absences$#', function (Request $req, Response $res, array $m) use ($absenceController) {
        $absenceController->findBySalarie($req, $res, (int)$m[1]);
    });
};