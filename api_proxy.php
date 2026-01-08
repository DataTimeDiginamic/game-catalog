<?php

// URL de base de ton API distante
$API_BASE = 'https://renard.alwaysdata.net';

// Autoriser l'appel depuis le navigateur
header('Content-Type: application/json; charset=utf-8');

// Construire l'URL distante
$path = $_GET['path'] ?? '';
$url = rtrim($API_BASE, '/') . '/' . ltrim($path, '/');

// Méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Données POST (si présentes)
$body = file_get_contents('php://input');

// Initialisation cURL
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_POSTFIELDS     => $body
]);

$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Renvoyer la réponse telle quelle
http_response_code($status ?: 200);
echo $response;
