<?php

namespace Controller;

use Core\Response;
use Core\Session;
use Core\Request;
use Repository\UtilisateurRepository;

final readonly class AuthController {

    public function __construct(
        private Response              $response,
        private UtilisateurRepository $utilisateurRepository,
        private Session               $session,
        private Request               $request
    ) {}

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
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        // Recherche dans la table 'utilisateur'
        $user = $this->utilisateurRepository->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Enregistrement des informations en session (id, droit admin/user)
            $this->session->set('user_id', $user['id_utilisateur']);
            $this->session->set('email', $user['email']);
            $this->session->set('droit', $user['droit']); // 'admin' ou 'user'

            $this->response->json([
                    'ok' => true,
                    'message' => 'Connexion réussie',
                    'user' => [
                        'email' => $user['email'],       // Adaptez selon le nom de la colonne en base
                        'role' => $user['droit']     // Correspond à 'admin' ou 'user' / 'client'
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
    public function loginForm() : void {
        // Si vous utilisez un système de vue ou un simple require :
        require_once __DIR__ . '/../../public/views/auth/login.php'; // Adaptez selon votre arborescence de vues
    }
}