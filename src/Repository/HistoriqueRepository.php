<?php

namespace Repository;

use PDO;

final readonly class HistoriqueRepository {

    public function __construct(private PDO $pdo) {}

    public function add(string $utilisateur, string $action, string $entite) : void {
        $sql = $this->pdo->prepare("INSERT INTO Historique (utilisateur, action, entite) VALUES (:utilisateur, :action, :entite)");
        $sql->execute(['utilisateur' => $utilisateur, 'action' => $action, 'entite' => $entite]);
    }

    public function findAll() : array {
        return $this->pdo->query("SELECT * FROM Historique ORDER BY id_historique DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
    }
}
