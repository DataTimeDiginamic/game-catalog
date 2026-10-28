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
}
