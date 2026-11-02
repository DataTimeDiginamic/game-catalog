<?php

namespace Controller;

use Core\Response;
use PDO;
use Repository\HistoriqueRepository;

// Historique des modifications, planning prévisionnel et salaires variables.
final readonly class SuiviController {

    public function __construct(
        private Response             $response,
        private HistoriqueRepository $historiqueRepository,
        private PDO                  $pdo
    ) {}

    // GET /api/historique
    public function historique() : void {
        $this->response->json($this->historiqueRepository->findAll());
    }

    // GET /api/planning : les tâches avec leur projet et leur salarié, classées par date de début.
    public function planning() : void {
        $sql = $this->pdo->query(
            "SELECT t.id_tache, t.Nom AS tache, p.nom AS projet, CONCAT(s.prenom, ' ', s.nom) AS salarie,
                    t.debut, t.fin, t.temps_previsionnel, t.temps_passe, t.statut
             FROM Tache t
             JOIN Projet p ON p.id_projet = t.id_projet
             JOIN Salarie s ON s.id_salarie = t.id_salarie
             ORDER BY t.debut"
        );
        $this->response->json($sql->fetchAll(PDO::FETCH_ASSOC));
    }

    // GET /api/salaires : jours passés x taux journalier moyen, pour chaque salarié.
    public function salaires() : void {
        $sql = $this->pdo->query(
            "SELECT s.id_salarie, CONCAT(s.prenom, ' ', s.nom) AS salarie, s.taux_journalier_moyen,
                    COALESCE(SUM(t.temps_previsionnel), 0) AS jours_prevus,
                    COALESCE(SUM(t.temps_passe), 0) AS jours_passes,
                    COALESCE(SUM(t.temps_previsionnel), 0) * s.taux_journalier_moyen AS salaire_prevu,
                    COALESCE(SUM(t.temps_passe), 0) * s.taux_journalier_moyen AS salaire_variable
             FROM Salarie s
             LEFT JOIN Tache t ON t.id_salarie = s.id_salarie AND t.statut <> 'annule'
             GROUP BY s.id_salarie, s.nom, s.prenom, s.taux_journalier_moyen
             ORDER BY s.nom"
        );
        $this->response->json($sql->fetchAll(PDO::FETCH_ASSOC));
    }
}
