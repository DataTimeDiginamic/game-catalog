<?php

namespace Controller;

use Core\Request;
use Core\Response;
use Core\Session;
use Repository\UtilisateurRepository;

// Espace administrateur : gestion des utilisateurs (l'accès admin est vérifié dans Core\Auth).
final readonly class UtilisateurController {

    private const ROLES = ['admin', 'recruteur', 'salarie', 'client'];

    public function __construct(
        private Response              $response,
        private UtilisateurRepository $utilisateurRepository,
        private Session               $session,
        private Request               $request
    ) {}

    public function index() : void {
        $this->response->json($this->utilisateurRepository->findAll());
    }

    public function show(int $id) : void {
        $utilisateur = $this->utilisateurRepository->findById($id);

        if ($utilisateur === null) {
            $this->response->json(['ok' => false, 'message' => 'Utilisateur introuvable'], 404);
            return;
        }

        $this->response->json($utilisateur);
    }

    public function store() : void {
        $nom = trim($this->request->post('nom'));
        $email = trim($this->request->post('email'));
        $motDePasse = $this->request->post('mot_de_passe');
        $role = trim($this->request->post('role'));

        $errors = $this->validate($nom, $email, $role);

        if (strlen($motDePasse) < 6) {
            $errors['mot_de_passe'] = 'Le mot de passe doit faire au moins 6 caractères';
        }

        if (!isset($errors['email']) && $this->utilisateurRepository->findByEmail($email) !== null) {
            $errors['email'] = 'Cet email est déjà utilisé';
        }

        if (!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        $id = $this->utilisateurRepository->insert([
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => password_hash($motDePasse, PASSWORD_DEFAULT),
            'role' => $role
        ]);

        $this->response->json(['ok' => true, 'message' => 'Utilisateur créé', 'id' => $id], 201);
    }

    public function update(int $id) : void {
        $nom = trim($this->request->post('nom'));
        $email = trim($this->request->post('email'));
        $role = trim($this->request->post('role'));
        $motDePasse = $this->request->post('mot_de_passe');

        if ($this->utilisateurRepository->findById($id) === null) {
            $this->response->json(['ok' => false, 'message' => 'Utilisateur introuvable'], 404);
            return;
        }

        $errors = $this->validate($nom, $email, $role);

        // Le mot de passe est facultatif à la modification.
        if ($motDePasse !== '' && strlen($motDePasse) < 6) {
            $errors['mot_de_passe'] = 'Le mot de passe doit faire au moins 6 caractères';
        }

        // L'email ne doit pas être pris par un autre utilisateur.
        $autre = $this->utilisateurRepository->findByEmail($email);
        if (!isset($errors['email']) && $autre !== null && (int) $autre['id_utilisateur'] !== $id) {
            $errors['email'] = 'Cet email est déjà utilisé';
        }

        if (!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        $this->utilisateurRepository->update($id, [
            'nom' => $nom,
            'email' => $email,
            'role' => $role
        ]);

        if ($motDePasse !== '') {
            $this->utilisateurRepository->updatePassword($id, password_hash($motDePasse, PASSWORD_DEFAULT));
        }

        $this->response->json(['ok' => true, 'message' => 'Utilisateur mis à jour']);
    }

    public function delete(int $id) : void {
        // On empêche l'admin de se supprimer lui-même.
        if ($id === (int) $this->session->get('user_id')) {
            $this->response->json(['ok' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte'], 400);
            return;
        }

        if ($this->utilisateurRepository->delete($id)) {
            $this->response->json(['ok' => true, 'message' => 'Utilisateur supprimé']);
        } else {
            $this->response->json(['ok' => false, 'message' => 'Utilisateur introuvable'], 404);
        }
    }

    private function validate(string $nom, string $email, string $role) : array {
        $errors = [];

        if ($nom === '') {
            $errors['nom'] = 'Veuillez renseigner un nom';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }

        if (!in_array($role, self::ROLES, true)) {
            $errors['role'] = 'Rôle invalide (admin, recruteur, salarie ou client)';
        }

        return $errors;
    }
}
