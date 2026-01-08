<?php

namespace Controller;

use Core\Request;
use Core\Session;
use Helper\Debug;
use Core\Response;


require_once __DIR__ . '/../Helper/Debug.php';

final readonly class AppController
{

    public GameApiController $api;

    public function __construct(
        private Response        $response,
        private Session         $session,
        private Request         $request,
    )

    {}

    public function home(): void
    {
        $this->response->json(["response" => "ok"]);
    }
}