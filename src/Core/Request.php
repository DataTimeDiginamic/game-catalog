<?php

namespace Core;

final class Request {

    public function path() : string {
        // 1. Récupérer uniquement le chemin de l'URI (sans la query string ?...)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        // 2. Récupérer le dossier parent du script d'entrée (ex: /DataTime/datatime/game-catalog/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        if ($scriptName !== '/' && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }

        // 3. Nettoyer au cas où index.php reste dans l'URI
        $uri = str_replace('/index.php', '', $uri);

        // 4. S'assurer que le chemin commence toujours par un /
        $uri = '/' . ltrim($uri, '/');

        return $uri === '' ? '/' : $uri;
    }

    public function method () : string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost () : bool {
        return $this->method() === 'POST';
    }

    public function post (string $key) : mixed {
        return $_POST[$key] ?? '';
    }
    public function json(): array { $input = file_get_contents("php://input"); return json_decode($input, true) ?? []; }
}
