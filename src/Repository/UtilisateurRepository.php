<?php

namespace Repository;

use PDO;

final readonly class UtilisateurRepository {

    public function __construct(private readonly PDO $pdo) {}

    public function findByEmail(string $email) : ?array {
        $sql = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $sql->execute([
            'email' => $email
        ]);

        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function createUser(array $data) : int {
        $sql = $this->pdo->prepare("INSERT INTO utilisateur (email, password, droit) VALUES (:email, :password, :droit)");
        $sql->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'droit' => $data['droit'] ?? 'user'
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
