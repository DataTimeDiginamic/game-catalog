<?php

namespace Repository;

use PDO;

readonly final class ClientsRepository {
    public function __construct(private readonly PDO $pdo) {}

    public function createClient(array $data) : int {
        $sql = $this->pdo->prepare("INSERT INTO Client (nom) VALUES (:nom)");
        $sql->execute([
            'nom' => $data['nom']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function findAllClients() : array {
        $sql = $this->pdo->query("SELECT * FROM Client ORDER BY id_client ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientById(int $id) : array {
        $sql = $this->pdo->prepare("SELECT * FROM Client WHERE id_client = :id");
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function searchClientsByName(string $name) : array {
        $sql = $this->pdo->prepare("SELECT * FROM Client WHERE nom LIKE :name ORDER BY id_client ASC");

        $sql->execute([
            'name' => '%' . $name . '%'
        ]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateClientName(int $id, string $newName) : bool {
        $sql = $this->pdo->prepare(" UPDATE Client SET nom = :nom WHERE id_client = :id ");
        $sql->execute(
            [
                'nom' => $newName,
                'id' => $id
            ]);

        return $sql->rowCount() > 0;
    }

    public function deleteClient(int $id) : bool {
        $sql = $this->pdo->prepare("DELETE FROM Client WHERE id_client = :id");
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->rowCount() > 0;
    }

    public function deleteClientByName(string $name) : bool {
        $sql = $this->pdo->prepare("DELETE FROM Client WHERE nom = :nom");
        $sql->bindValue(':nom', $name, PDO::PARAM_STR);
        $sql->execute();

        return $sql->rowCount() > 0;
    }
}