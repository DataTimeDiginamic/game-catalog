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

    public function findAllProjects() : array {
        $sql = $this->pdo->query("SELECT * FROM Projet ORDER BY id_projet ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectById(int $id) : array {
        $sql = $this->pdo->prepare("SELECT * FROM Projet WHERE id_projet = :id");
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
}