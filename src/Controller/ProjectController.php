<?php

namespace Controller;

use JetBrains\PhpStorm\NoReturn;
use Core\Response;
use Core\Session;
use Core\Request;
use Repository\ProjectsRepository;

final readonly class ProjectController {

    public function __construct(
        private Response $response,
        private ProjectsRepository $projectsRepository,
        private Session $session,
        private Request $request
    ) {}

    public function handleAddProject() : void {
        $nom = trim($this->request->post("nom"));

        $errors = [];

        if($nom === '') $errors['nom'] = 'Veuillez renseigner un nom de projet';

        $old = [
            'nom' => $nom
        ];

        if(!empty($errors)) {
            return;
        }

        $this->projectsRepository->createProject($old);
    }
}