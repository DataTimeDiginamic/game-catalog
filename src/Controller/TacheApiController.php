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

        // Une date de fin vide veut dire "pas de date de fin".
        if (($data['fin'] ?? '') === '') {
            $data['fin'] = null;
        }

        $errors = $this->validate($data);
        if (!empty($errors)) {
            $this->response->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

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

        // Une date de fin vide veut dire "pas de date de fin".
        if (($data['fin'] ?? '') === '') {
            $data['fin'] = null;
        }

        $errors = $this->validate($data);
        if (!empty($errors)) {
            $this->response->json(['success' => false, 'errors' => $errors], 400);
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

    /** Vérifie les données d'une tâche : retourne la liste des erreurs (vide si tout est bon) */
    private function validate(array $data): array
    {
        $errors = [];

        $nom = trim((string)($data['Nom'] ?? ''));
        if ($nom === '' || strlen($nom) > 50) {
            $errors['Nom'] = 'Le nom est obligatoire (50 caractères maximum)';
        }

        if (!in_array($data['statut'] ?? '', ['a_faire', 'en_cours', 'termine'], true)) {
            $errors['statut'] = 'Statut invalide (a_faire, en_cours ou termine)';
        }

        if (!is_numeric($data['temps_previsionnel'] ?? null) || $data['temps_previsionnel'] < 0) {
            $errors['temps_previsionnel'] = 'Le temps prévisionnel doit être un nombre positif';
        }

        if (!is_numeric($data['temps_passe'] ?? null) || $data['temps_passe'] < 0) {
            $errors['temps_passe'] = 'Le temps passé doit être un nombre positif';
        }

        if (!$this->isDate($data['debut'] ?? '')) {
            $errors['debut'] = 'La date de début est invalide (AAAA-MM-JJ)';
        }

        // La date de fin est facultative.
        $fin = $data['fin'] ?? '';
        if ($fin !== '' && $fin !== null) {
            if (!$this->isDate($fin)) {
                $errors['fin'] = 'La date de fin est invalide (AAAA-MM-JJ)';
            } elseif (!isset($errors['debut']) && $fin < $data['debut']) {
                $errors['fin'] = 'La date de fin doit être après la date de début';
            }
        }

        if ((int)($data['id_projet'] ?? 0) <= 0) {
            $errors['id_projet'] = 'Le projet est obligatoire';
        }

        if ((int)($data['id_salarie'] ?? 0) <= 0) {
            $errors['id_salarie'] = 'Le salarié est obligatoire';
        }

        return $errors;
    }

    /** Vrai si la valeur est une date réelle au format AAAA-MM-JJ */
    private function isDate(mixed $value): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', (string)$value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
