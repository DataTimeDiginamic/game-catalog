<?php

namespace Core;

final class Request {

    public function path() : string {
        $path = $_SERVER['REQUEST_URI'];
        return is_string($path) && $path!== '' ? $path : '/';
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
