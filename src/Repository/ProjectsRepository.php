<?php

namespace Repository;

use PDO;

readonly final class ProjectsRepository {
    public function __construct(private readonly PDO $pdo) {}

    public function createProject(array $data) : int {
        $sql = $this->pdo->prepare("INSERT INTO Projet (nom) VALUES (:nom)");
        $sql->execute([
            'nom' => $data['nom']
        ]);

        return $this->pdo->lastInsertId();
    }
}