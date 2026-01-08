<?php

namespace Controller;

use Core\Request;
use Core\Response;
use Repository\TacheRepository;

final class TacheApiController
{
    public function __construct(
        private Response $response,
        private TacheRepository $tacheRepository,
        private Request $request
    ) {}

    /** GET /taches */
    public function index(Request $req, Response $res): void
    {
        $taches = $this->tacheRepository->findAll();
        $this->response->json($taches);
    }

    /** GET /taches/:id */
    public function show(Request $req, Response $res, int $id): void
    {
        $tache = $this->tacheRepository->findById($id);

        if ($tache === null) {
            $this->response->json(['error' => 'Tache not found'], 404);
            return;
        }

        $this->response->json($tache);
    }

    /** POST /taches */
    public function store(): void
    {
        // Lecture JSON
        $data = $this->request->json();

        $newId = $this->tacheRepository->insert($data);

        $this->response->json([
            'success' => true,
            'id_tache' => $newId,
            'data' => $data
        ], 201);
    }

    /** POST /taches/update */
    public function update(Request $req, Response $res): void
    {
        // Lecture JSON
        $data = $this->request->json();
        $id = (int)($data['id_tache'] ?? 0);

        if ($id === 0) {
            $this->response->json(['error' => 'Missing id_tache'], 400);
            return;
        }

        $success = $this->tacheRepository->update($id, $data);

        $this->response->json([
            'success' => $success,
            'updated' => $data
        ]);
    }

    /** POST /taches/delete */
    public function delete(Request $req, Response $res): void
    {
        // Lecture JSON
        $data = $this->request->json();
        $id = (int)($data['id_tache'] ?? 0);

        if ($id === 0) {
            $this->response->json(['error' => 'Missing id_tache'], 400);
            return;
        }

        $success = $this->tacheRepository->delete($id);

        $this->response->json([
            'success' => $success
        ]);
    }
}
