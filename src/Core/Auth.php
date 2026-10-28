<?php

namespace Core;

// Vérifie qu'un utilisateur est connecté et qu'il a le droit de faire la requête (selon son rôle).
final class Auth {

    // Pages accessibles sans être connecté.
    private const PUBLIC_PATHS = ['/api/ping', '/api/login', '/api/logout', '/api/me'];

    public function __construct(
        private Session  $session,
        private Response $response
    ) {}

    // Retourne true si la requête peut continuer, sinon envoie l'erreur et retourne false.
    public function check(Request $request) : bool {
        $path = $request->path();
        $method = $request->method();

        if (in_array($path, self::PUBLIC_PATHS, true)) {
            return true;
        }

        $role = $this->session->get('user_role');

        // Pas connecté (ou ancienne session sans rôle : il faut se reconnecter).
        if ($this->session->get('user_id') === null || $role === null) {
            $this->response->json(['ok' => false, 'message' => 'Non connecté'], 401);
            return false;
        }

        if (!$this->canAccess($role, $method, $path)) {
            $this->response->json(['ok' => false, 'message' => 'Accès interdit pour votre rôle'], 403);
            return false;
        }

        return true;
    }

    // Les droits de chaque rôle.
    private function canAccess(?string $role, string $method, string $path) : bool {
        // L'admin peut tout faire.
        if ($role === 'admin') {
            return true;
        }

        // Les utilisateurs sont réservés à l'admin.
        if (str_starts_with($path, '/api/utilisateurs')) {
            return false;
        }

        // Le recruteur peut tout faire sauf gérer les utilisateurs.
        if ($role === 'recruteur') {
            return true;
        }

        // Le salarié peut tout lire, et gérer ses absences.
        if ($role === 'salarie') {
            return $method === 'GET' || str_starts_with($path, '/api/absences');
        }

        // Le client peut seulement lire les projets et les tâches.
        if ($role === 'client') {
            return $method === 'GET' && (str_starts_with($path, '/api/projet') || str_starts_with($path, '/api/taches'));
        }

        return false;
    }
}
