<?php
namespace Repository;

use PDO;

readonly final class TacheRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Récupérer toutes les tâches */
    public function findAll(): array
    {
        $sql = $this->pdo->query('SELECT * FROM Tache');
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Récupérer une tâche par son ID */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Tache WHERE id_tache = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $tache = $stmt->fetch(PDO::FETCH_ASSOC);
        return $tache === false ? null : $tache;
    }

    /** Insérer une nouvelle tâche */
    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Tache (Nom, temps_previsionnel, temps_passe, debut, fin, statut, id_projet, id_salarie)
             VALUES (:Nom, :temps_previsionnel, :temps_passe, :debut, :fin, :statut, :id_projet, :id_salarie)'
        );

        $stmt->execute([
            'Nom' => $data['Nom'],
            'temps_previsionnel' => $data['temps_previsionnel'],
            'temps_passe' => $data['temps_passe'],
            'debut' => $data['debut'],
            'fin' => $data['fin'],
            'statut' => $data['statut'],
            'id_projet' => $data['id_projet'],
            'id_salarie' => $data['id_salarie']
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /** Mettre à jour une tâche */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Tache SET 
                Nom = :Nom,
                temps_previsionnel = :temps_previsionnel,
                temps_passe = :temps_passe,
                debut = :debut,
                fin = :fin,
                statut = :statut,
                id_projet = :id_projet,
                id_salarie = :id_salarie
             WHERE id_tache = :id'
        );

        $stmt->execute([
            'id' => $id,
            'Nom' => $data['Nom'],
            'temps_previsionnel' => $data['temps_previsionnel'],
            'temps_passe' => $data['temps_passe'],
            'debut' => $data['debut'],
            'fin' => $data['fin'],
            'statut' => $data['statut'],
            'id_projet' => $data['id_projet'],
            'id_salarie' => $data['id_salarie']
        ]);
        return $stmt->rowCount() > 0 ;
            }

    /** Supprimer une tâche */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Tache WHERE id_tache = :id');
        return $stmt->execute(['id' => $id]);
    }
}
