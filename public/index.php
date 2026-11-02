<?php

use Controller\AuthController;
use Controller\UtilisateurController;
use Controller\SuiviController;
use Controller\EntrepriseController;
use Controller\PingApiController;
use Controller\ProjectController;
use Controller\ClientController;
use Controller\AbsenceController;
use Controller\SalarieApiController;
use Controller\TacheApiController;
use Core\Auth;
use Core\Cors;
use Core\Database;
use Core\Response;
use Core\Session;
use Core\Request;
use Core\Router;

use Repository\ProjectsRepository;
use Repository\ClientsRepository;
use Repository\SalarieRepository;
use Repository\TacheRepository;
use Repository\UtilisateurRepository;
use Repository\HistoriqueRepository;

session_start();
require __DIR__ . '/../autoload.php';
$registerProjectRoutes = require __DIR__ . '/../config/projectRoutes.php';
$registerClientsRoutes = require __DIR__ . '/../config/clientRoutes.php';
$registerAbsenceRoutes = require __DIR__ . '/../config/absenceRoutes.php';
$registerTachesRoutes = require __DIR__ . '/../config/tachesRoutes.php';
$registerSalarieRoutes = require __DIR__ . '/../config/salarieRoutes.php';
$registerAuthRoutes = require __DIR__ . '/../config/authRoutes.php';
$registerUtilisateurRoutes = require __DIR__ . '/../config/utilisateurRoutes.php';
$registerSuiviRoutes = require __DIR__ . '/../config/suiviRoutes.php';
$registerEntrepriseRoutes = require __DIR__ . '/../config/entrepriseRoutes.php';
$config = require_once __DIR__ . '/../config/db.php';

Cors::Handle();

$response = new Response();
$session = new Session();
$request = new Request();
$router = new Router();

$projectRepository = new ProjectsRepository(Database::makePdo($config['db']));
$clientRepository = new ClientsRepository(Database::makePdo($config['db']));
$tacheRepository = new TacheRepository(Database::makePdo($config['db']));
$salarieRepository = new SalarieRepository(Database::makePdo($config['db']));
$utilisateurRepository = new UtilisateurRepository(Database::makePdo($config['db']));
$historiqueRepository = new HistoriqueRepository(Database::makePdo($config['db']));

$pingApiController = new PingApiController();
$projectController = new ProjectController($response, $projectRepository, $session, $request);
$clientController = new ClientController($response, $clientRepository, $session, $request);
$absenceController = new AbsenceController(Database::makePdo($config['db']));
$tacheApiController = new TacheApiController($response, $tacheRepository, $request);
$salarieController = new SalarieApiController($response, $salarieRepository, $session, $request);
$authController = new AuthController($response, $utilisateurRepository, $session, $request);
$utilisateurController = new UtilisateurController($response, $utilisateurRepository, $session, $request);
$suiviController = new SuiviController($response, $historiqueRepository, Database::makePdo($config['db']));
$entrepriseController = new EntrepriseController($response);
$auth = new Auth($session, $response);

$registerProjectRoutes($router, $projectController, $pingApiController, $projectRepository);
$registerClientsRoutes($router, $clientController, $pingApiController, $clientRepository);
$registerAbsenceRoutes($router, $pingApiController, $absenceController);
$registerTachesRoutes($router, $pingApiController, $tacheApiController, $tacheRepository);
$registerSalarieRoutes($router, $salarieController);
$registerAuthRoutes($router, $authController);
$registerUtilisateurRoutes($router, $utilisateurController);
$registerSuiviRoutes($router, $suiviController);
$registerEntrepriseRoutes($router, $entrepriseController);

// Il faut être connecté (et avoir le bon rôle) pour utiliser l'API.
if (!$auth->check($request)) {
    exit;
}

try {
    $router->dispatch($request, $response);

    // On garde une trace de chaque création / modification / suppression réussie.
    if ($request->method() !== 'GET' && $request->path() !== '/api/login' && http_response_code() < 400) {
        $path = $request->path();
        $action = 'creation';
        if ($request->method() === 'DELETE' || str_contains($path, 'delete')) {
            $action = 'suppression';
        } elseif ($request->method() === 'PUT' || str_contains($path, 'update') || preg_match('#/\d+$#', $path)) {
            $action = 'modification';
        }
        $entite = explode('/', trim($path, '/'))[1] ?? '';
        $historiqueRepository->add((string) $session->get('user_nom'), $action, $entite);
    }
} catch (PDOException $e) {
    // Erreur SQL : on répond en JSON (au lieu d'une page d'erreur PHP) pour que le front puisse l'afficher.
    if ($e->getCode() === '23000') {
        // Clé étrangère inexistante, ou donnée encore utilisée ailleurs, ou doublon.
        $response->json(['ok' => false, 'message' => 'Donnée liée inexistante, doublon ou encore utilisée ailleurs'], 409);
    } else {
        $response->json(['ok' => false, 'message' => 'Erreur de base de données'], 500);
    }
}