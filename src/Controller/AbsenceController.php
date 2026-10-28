<?php

namespace Controller;

use Core\Request;
use Core\Response;
use PDO;

final class AbsenceController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(Request $request, Response $response): void
    {
        $repository = new \Repository\AbsenceRepository($this->pdo);
        $absences = $repository->findAll();
        $response->json(['success' => true, 'data' => $absences]);
    }

    public function show(Request $request, Response $response, int $id): void
    {
        $repository = new \Repository\AbsenceRepository($this->pdo);
        $absence = $repository->findById($id);
        
        if (!$absence) {
            $response->json(['success' => false, 'message' => 'Absence not found'], 404);
            return;
        }
        
        $response->json(['success' => true, 'data' => $absence]);
    }

    public function store(Request $request, Response $response): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $required = ['type', 'debut', 'id_salarie'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $response->json(['success' => false, 'message' => "Missing required field: $field"], 400);
                return;
            }
        }
        
        $validTypes = ['conge', 'maladie'];
        if (!in_array($data['type'], $validTypes)) {
            $response->json(['success' => false, 'message' => 'Invalid type. Must be conge or maladie'], 400);
            return;
        }
        
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $response->json(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            return;
        }

        $repository = new \Repository\AbsenceRepository($this->pdo);
        $success = $repository->create($data);
        
        if ($success) {
            $response->json(['success' => true, 'message' => 'Absence created'], 201);
        } else {
            $response->json(['success' => false, 'message' => 'Failed to create absence'], 500);
        }
    }

    public function update(Request $request, Response $response, int $id): void
    {
        $repository = new \Repository\AbsenceRepository($this->pdo);
        $absence = $repository->findById($id);
        
        if (!$absence) {
            $response->json(['success' => false, 'message' => 'Absence not found'], 404);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        
        if (!empty($data['type'])) {
            $validTypes = ['conge', 'maladie'];
            if (!in_array($data['type'], $validTypes)) {
                $response->json(['success' => false, 'message' => 'Invalid type. Must be conge or maladie'], 400);
                return;
            }
        }
        
        // Merge with existing data
        $updateData = array_merge($absence, $data);

        $errors = $this->validate($updateData);
        if (!empty($errors)) {
            $response->json(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            return;
        }
        
        $success = $repository->update($id, $updateData);
        
        if ($success) {
            $response->json(['success' => true, 'message' => 'Absence updated']);
        } else {
            $response->json(['success' => false, 'message' => 'Failed to update absence'], 500);
        }
    }

    public function delete(Request $request, Response $response, int $id): void
    {
        $repository = new \Repository\AbsenceRepository($this->pdo);
        $absence = $repository->findById($id);
        
        if (!$absence) {
            $response->json(['success' => false, 'message' => 'Absence not found'], 404);
            return;
        }
        
        $success = $repository->delete($id);
        
        if ($success) {
            $response->json(['success' => true, 'message' => 'Absence deleted']);
        } else {
            $response->json(['success' => false, 'message' => 'Failed to delete absence'], 500);
        }
    }

    /** Vérifie les données d'une absence : retourne la liste des erreurs (vide si tout est bon) */
    private function validate(array $data): array
    {
        $errors = [];

        $debut = \DateTime::createFromFormat('Y-m-d', (string)($data['debut'] ?? ''));
        if ($debut === false || $debut->format('Y-m-d') !== $data['debut']) {
            $errors['debut'] = 'La date de début est invalide (AAAA-MM-JJ)';
        }

        // La date de fin est facultative.
        $fin = $data['fin'] ?? null;
        if ($fin !== null && $fin !== '') {
            $finDate = \DateTime::createFromFormat('Y-m-d', (string)$fin);
            if ($finDate === false || $finDate->format('Y-m-d') !== $fin) {
                $errors['fin'] = 'La date de fin est invalide (AAAA-MM-JJ)';
            } elseif (!isset($errors['debut']) && $fin < $data['debut']) {
                $errors['fin'] = 'La date de fin doit être après la date de début';
            }
        }

        if (strlen((string)($data['motif'] ?? '')) > 50) {
            $errors['motif'] = 'Le motif ne doit pas dépasser 50 caractères';
        }

        if ((int)($data['id_salarie'] ?? 0) <= 0) {
            $errors['id_salarie'] = 'Le salarié est obligatoire';
        }

        return $errors;
    }

    public function findBySalarie(Request $request, Response $response, int $salarieId): void
    {
        $repository = new \Repository\AbsenceRepository($this->pdo);
        $absences = $repository->findBySalarieId($salarieId);
        $response->json(['success' => true, 'data' => $absences]);
    }
}
