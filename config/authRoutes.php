<?php
// config/authRoutes.php

use Controller\AuthController;
use Core\Router;

return function (
    Router $router,
    AuthController $authController
) {
    // -------------------------
    // 🔐 ROUTES AUTHENTIFICATION
    // -------------------------

    // Afficher le formulaire de connexion (GET)
    $router->get('/login', function () use ($authController) {
        $authController->loginForm();
    });

    // Traiter la soumission du formulaire de connexion (POST)
    $router->post('/login', function () use ($authController) {
        $authController->login();
    });

    // Déconnexion (GET)
    $router->get('/logout', function () use ($authController) {
        $authController->logout();
    });
};