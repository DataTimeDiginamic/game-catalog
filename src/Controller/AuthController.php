<?php

namespace Controller;

use Core\Request;
use Core\Response;
use Core\Session;
use Repository\UtilisateurRepository;

final readonly class AuthController {

    public function __construct(
        private Response              $response,
        private UtilisateurRepository $utilisateurRepository,
        private Session               $session,
        private Request               $request
    ) {}

    // Connexion et déconnexion : code de Bill (branche utilisateur), adapté à la table Utilisateur (nom, mot_de_passe, role).
    public function login() : void {
        $email = trim($this->request->post("email"));
        $password = $this->request->post("mot_de_passe");

        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Veuillez renseigner un email';
        }

        if ($password === '') {
            $errors['password'] = 'Veuillez renseigner un mot de passe';
        }

        if (!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors, 'message' => 'Veuillez remplir tous les champs'], 400);
            return;
        }

        // Recherche dans la table 'Utilisateur'
        $user = $this->utilisateurRepository->findByEmail($email);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Enregistrement des informations en session (id, nom, rôle)
            session_regenerate_id(true);
            $this->session->set('user_id', $user['id_utilisateur']);
            $this->session->set('user_nom', $user['nom']);
            $this->session->set('user_role', $user['role']);

            $this->response->json([
                'ok' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user['id_utilisateur'],
                    'nom' => $user['nom'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            $this->response->json([
                'ok' => false,
                'message' => 'Identifiants incorrects'
            ], 401);
        }
    }

    public function logout() : void {
        // Destruction de la session
        session_unset();
        session_destroy();

        $this->response->json([
            'ok' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function me() : void {
        $id = $this->session->get('user_id');

        // Une ancienne session sans rôle compte comme non connectée.
        if ($id === null || $this->session->get('user_role') === null) {
            $this->response->json(['ok' => false, 'message' => 'Non connecté'], 401);
            return;
        }

        $this->response->json([
            'ok' => true,
            'user' => [
                'id' => $id,
                'nom' => $this->session->get('user_nom'),
                'role' => $this->session->get('user_role')
            ]
        ]);
    }
}
