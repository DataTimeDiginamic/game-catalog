<?php

namespace Controller;

use Core\Response;
use Core\Session;
use Core\Request;
use Repository\ClientsRepository;

final readonly class ClientController {

    public function __construct(
        private Response          $response,
        private ClientsRepository $clientsRepository,
        private Session           $session,
        private Request           $request
    ) {}

    public function handleAddClient() : void {
        $nom = trim($this->request->post("nom"));

        $errors = [];

        if($nom === '') {
            $errors['nom'] = 'Veuillez renseigner un nom de client';
        }

        $old = [
            'nom' => $nom
        ];

        if(!empty($errors)) {
            $this->response->json(['ok' => false, 'errors' => $errors], 400);
            return;
        }

        $this->clientsRepository->createClient($old);

        $this->response->json(['ok' => true, 'message' => 'Client créé']);
    }

    public function getClients(Request $request, ClientsRepository $clientsRepository, Response $response) : void {
        $response->json($clientsRepository->findAllClients());
    }

    public function getById(Request $request, ClientsRepository $clientsRepository, Response $response, int $id) : void {
        $response->json($clientsRepository->getClientById($id));
    }

    public function getByName(Request $request, Response $response, string $name) : void {
        $name = trim($name);

        if ($name === '') {
            $response->json(['ok' => false, 'message' => 'Veuillez renseigner un nom'], 400);
            return;
        }

        $results = $this->clientsRepository->searchClientsByName($name);

        $response->json([
            'ok' => true,
            'count' => count($results),
            'results' => $results
        ]);
    }


    public function updateClient(Request $request, Response $response) : void {
        $id = (int) $request->post('id');
        $newName = trim($request->post('nom'));

        $errors = [];

        if ($id <= 0) {
            $errors['id'] = 'ID invalide';
        }

        if ($newName === '') {
            $errors['nom'] = 'Veuillez renseigner un nom de client';
        }

        if (!empty($errors)) {
            $response->json(['ok' => false, 'errors' => $errors], 400); return;
        } $updated = $this->clientsRepository->updateClientName($id, $newName);

        if ($updated) {
            $response->json(['ok' => true, 'message' => 'Client mis à jour']);
        } else {
            $response->json(['ok' => false, 'message' => 'Client introuvable'], 404);
        }
    }

    public function deleteClient(Request $request, Response $response) : void {
        $id = (int) $request->post('id');

        if ($id <= 0) {
            $response->json(['ok' => false, 'message' => 'ID invalide'], 400);
            return;
        }

        $deleted = $this->clientsRepository->deleteClient($id);

        if ($deleted) {
            $response->json(['ok' => true, 'message' => 'Client supprimé']);
        } else {
            $response->json(['ok' => false, 'message' => 'Client introuvable'], 404);
        }
    }

    public function deleteClientByName(Request $request, Response $response) : void {
        $name = trim($request->post('nom'));

        if ($name === '') {
            $response->json(['ok' => false, 'message' => 'Nom de client invalide'], 400);
            return;
        }

        $deleted = $this->clientsRepository->deleteClientByName($name);

        if ($deleted) {
            $response->json(['ok' => true, 'message' => 'Client supprimé']);
        } else {
            $response->json(['ok' => false, 'message' => 'Client introuvable'], 404);
        }
    }
}