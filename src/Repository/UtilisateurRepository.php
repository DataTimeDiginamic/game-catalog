<?php

namespace Repository;

use PDO;

final readonly class UtilisateurRepository {
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email) : ?array {
        $sql = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $sql->execute(['email' => $email]);
        $utilisateur = $sql->fetch(PDO::FETCH_ASSOC);
        return $utilisateur === false ? null : $utilisateur;
    }

    // On ne renvoie jamais le mot de passe dans les listes.
    public function findAll() : array {
        $sql = $this->pdo->query("SELECT id_utilisateur, nom, email, role FROM Utilisateur ORDER BY id_utilisateur ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id) : ?array {
        $sql = $this->pdo->prepare("SELECT id_utilisateur, nom, email, role FROM Utilisateur WHERE id_utilisateur = :id");
        $sql->execute(['id' => $id]);
        $utilisateur = $sql->fetch(PDO::FETCH_ASSOC);
        return $utilisateur === false ? null : $utilisateur;
    }

    public function insert(array $data) : int {
        $sql = $this->pdo->prepare("INSERT INTO Utilisateur (nom, email, mot_de_passe, role) VALUES (:nom, :email, :mot_de_passe, :role)");
        $sql->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'mot_de_passe' => $data['mot_de_passe'],
            'role' => $data['role']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data) : bool {
        $sql = $this->pdo->prepare("UPDATE Utilisateur SET nom = :nom, email = :email, role = :role WHERE id_utilisateur = :id");
        $sql->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'role' => $data['role'],
            'id' => $id
        ]);

        return $sql->rowCount() > 0;
    }

    public function updatePassword(int $id, string $motDePasse) : void {
        $sql = $this->pdo->prepare("UPDATE Utilisateur SET mot_de_passe = :mot_de_passe WHERE id_utilisateur = :id");
        $sql->execute(['mot_de_passe' => $motDePasse, 'id' => $id]);
    }

    public function delete(int $id) : bool {
        $sql = $this->pdo->prepare("DELETE FROM Utilisateur WHERE id_utilisateur = :id");
        $sql->execute(['id' => $id]);

        return $sql->rowCount() > 0;
    }
}
