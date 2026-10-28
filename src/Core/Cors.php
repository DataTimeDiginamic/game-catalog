<?php

namespace Core;

final class Cors
{
    public static function handle () :void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // On accepte le front en local (PhpStorm, serveur de dev...), peu importe le port.
        if ($origin === 'http://localhost' || str_starts_with($origin, 'http://localhost:')) {
            // On laisse passer la communication.
            header("Access-Control-Allow-Origin: $origin");
            // Nécessaire pour envoyer le cookie de session (connexion).
            header("Access-Control-Allow-Credentials: true");
        }

        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
