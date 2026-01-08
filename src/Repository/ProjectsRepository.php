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

    public function updateProjectName(int $id, string $newName) : bool {
        $sql = $this->pdo->prepare(" UPDATE Projet SET nom = :nom WHERE id_projet = :id ");
        $sql->execute(
            [
                'nom' => $newName,
                'id' => $id
            ]);

        return $sql->rowCount() > 0;
    }

    public function deleteProject(int $id) : bool {
        $sql = $this->pdo->prepare("DELETE FROM Projet WHERE id_projet = :id");
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->rowCount() > 0;
    }

    public function deleteProjectByName(string $name) : bool {
        $sql = $this->pdo->prepare("DELETE FROM Projet WHERE nom = :nom");
        $sql->bindValue(':nom', $name, PDO::PARAM_STR);
        $sql->execute();

        return $sql->rowCount() > 0;
    }
}