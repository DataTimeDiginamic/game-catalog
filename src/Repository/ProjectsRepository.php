<?php

namespace Repository;

use PDO;

readonly final class ProjectsRepository {
    public function __construct(private readonly PDO $pdo) {}

    public function createProject(array $data) : int {
        $sql = $this->pdo->prepare("INSERT INTO Projet (id_projet, nom) VALUES (:id, :nom)");
        $sql->execute([
            'id' => $data['id'],
            'nom' => $data['nom']
        ]);

        return $this->pdo->lastInsertId();
    }
}