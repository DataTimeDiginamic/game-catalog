<?php

namespace Core;

final class Request {

    public function path() : string {
        // On enlève la query string (?a=b).
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Sous Laragon, l'API est dans un sous-dossier (ex: /datatime/game-catalog/public).
        // On l'enlève pour retrouver /api/client, /api/login...
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return is_string($path) && $path !== '' ? $path : '/';
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
