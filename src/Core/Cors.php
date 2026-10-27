<?php

namespace Core;

final class Cors
{
    public static function handle () :void {
        $allowedorigin = 'http://localhost:4200';
        $allowedOrigin = 'http://localhost:63342';
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($origin === $allowedOrigin) {
            // On laisse passer la communication.
            header("Access-Control-Allow-Origin: $allowedOrigin");
            // Si on se servait de la session et cookies + log in.
            // header("Access-Control-Allow-Credentials: true");
        }

        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}