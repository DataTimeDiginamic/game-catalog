<?php

namespace Controller;

use Core\Response;
use Core\Session;
use Core\Request;
use Repository\ProjectsRepository;

final readonly class ProjectController {

    public function __construct(
        private Response            $response,
        private ProjectsRepository  $projectsRepository,
        private Session             $session,
        private Request             $request
    ) {}

    public function handleAddProject() : void {
        $nom = trim($this->request->post("nom"));

        $errors = [];

        if($nom === '') {
            $errors['nom'] = 'Veuillez renseigner un nom de projet';
        }

        $old = [
            'nom' => $nom
        ];

        if(!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        $this->projectsRepository->createProject($old);

        $this->response->json(['ok' => true, 'message' => 'Projet créé']);
    }

    public function getProjects(Request $request, ProjectsRepository $projectsRepository, Response $response) : void {
        $response->json($projectsRepository->findAllProjects());
    }

    public function getById(Request $request, ProjectsRepository $projectsRepository, Response $response, int $id) : void {
        $response->json($projectsRepository->getProjectById($id));
    }

    public function getByName(Request $request, Response $response, string $name) : void {
        $name = trim($name);

        if ($name === '') {
            $response->json(['ok' => false, 'message' => 'Veuillez renseigner un nom'], 400);
            return;
        }

        $results = $this->projectsRepository->searchProjectsByName($name);

        $response->json([
            'ok' => true,
            'count' => count($results),
            'results' => $results
        ]);
    }


    public function updateProject(Request $request, Response $response) : void {
        $id = (int) $request->post('id');
        $newName = trim($request->post('nom'));

        $errors = [];

        if ($id <= 0) {
            $errors['id'] = 'ID invalide';
        }

        if ($newName === '') {
            $errors['nom'] = 'Veuillez renseigner un nom de projet';
        }

        if (!empty($errors)) {
            $response->json(['ok' => false, 'errors' => $errors], 400); return;
        } $updated = $this->projectsRepository->updateProjectName($id, $newName);

        if ($updated) {
            $response->json(['ok' => true, 'message' => 'Projet mis à jour']);
        } else {
            $response->json(['ok' => false, 'message' => 'Projet introuvable'], 404);
        }
    }

    public function deleteProject(Request $request, Response $response) : void {
        $id = (int) $request->post('id');

        if ($id <= 0) {
            $response->json(['ok' => false, 'message' => 'ID invalide'], 400);
            return;
        }

        $deleted = $this->projectsRepository->deleteProject($id);

        if ($deleted) {
            $response->json(['ok' => true, 'message' => 'Projet supprimé']);
        } else {
            $response->json(['ok' => false, 'message' => 'Projet introuvable'], 404);
        }
    }

    public function deleteProjectByName(Request $request, Response $response) : void {
        $name = trim($request->post('nom'));

        if ($name === '') {
            $response->json(['ok' => false, 'message' => 'Nom de projet invalide'], 400);
            return;
        }

        $deleted = $this->projectsRepository->deleteProjectByName($name);

        if ($deleted) {
            $response->json(['ok' => true, 'message' => 'Projet supprimé']);
        } else {
            $response->json(['ok' => false, 'message' => 'Projet introuvable'], 404);
        }
    }
}