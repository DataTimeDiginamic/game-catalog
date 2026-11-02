<?php

namespace Controller;

use Core\Response;

// Recherche d'une entreprise par son SIREN (9 chiffres) ou son SIRET (14 chiffres)
// avec l'API publique "Recherche d'entreprises" (pas de clé nécessaire).
final readonly class EntrepriseController {

    public function __construct(private Response $response) {}

    // GET /api/entreprise/{numero}
    public function rechercher(string $numero) : void {
        // Un SIRET commence par le SIREN : on garde les 9 premiers chiffres.
        $numero = preg_replace('/\s+/', '', $numero);
        if (!preg_match('/^(\d{9}|\d{14})$/', $numero)) {
            $this->response->json(['ok' => false, 'message' => 'Saisir un SIREN (9 chiffres) ou un SIRET (14 chiffres)'], 400);
            return;
        }
        $siren = substr($numero, 0, 9);

        $json = @file_get_contents('https://recherche-entreprises.api.gouv.fr/search?q=' . $siren . '&per_page=1');
        if ($json === false) {
            $this->response->json(['ok' => false, 'message' => "L'API des entreprises est injoignable"], 502);
            return;
        }

        $resultats = json_decode($json, true)['results'] ?? [];
        if (count($resultats) === 0) {
            $this->response->json(['ok' => false, 'message' => 'Aucune entreprise trouvée'], 404);
            return;
        }

        $this->response->json([
            'ok' => true,
            'siren' => $resultats[0]['siren'],
            'nom' => $resultats[0]['nom_complet'],
            'adresse' => $resultats[0]['siege']['adresse'] ?? ''
        ]);
    }
}
