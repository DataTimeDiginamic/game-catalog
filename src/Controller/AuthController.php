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

    public function login() : void {
        $email = trim($this->request->post('email'));
        $motDePasse = $this->request->post('mot_de_passe');

        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Veuillez renseigner un email';
        }

        if ($motDePasse === '') {
            $errors['mot_de_passe'] = 'Veuillez renseigner un mot de passe';
        }

        if (!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        $utilisateur = $this->utilisateurRepository->findByEmail($email);

        if ($utilisateur === null || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            $this->response->json(['ok' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
            return;
        }

        session_regenerate_id(true);
        $this->session->set('user_id', $utilisateur['id_utilisateur']);
        $this->session->set('user_nom', $utilisateur['nom']);

        $this->response->json([
            'ok' => true,
            'message' => 'Connecté',
            'user' => [
                'id' => $utilisateur['id_utilisateur'],
                'nom' => $utilisateur['nom']
            ]
        ]);
    }

    public function logout() : void {
        $_SESSION = [];
        session_destroy();

        $this->response->json(['ok' => true, 'message' => 'Déconnecté']);
    }

    public function me() : void {
        $id = $this->session->get('user_id');

        if ($id === null) {
            $this->response->json(['ok' => false, 'message' => 'Non connecté'], 401);
            return;
        }

        $this->response->json([
            'ok' => true,
            'user' => [
                'id' => $id,
                'nom' => $this->session->get('user_nom')
            ]
        ]);
    }
}
