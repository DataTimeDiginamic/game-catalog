<?php
// scripts/seed-utilisateur.php

require_once __DIR__ . '/../autoload.php';

try {
    // 1. Récupération du tableau de configuration depuis config/db.php (ou db.local.php)
    $dbConfig = require_once __DIR__ . '/../config/db.php';

    // Si config/db.php retourne un tableau de paramètres :
    $host = $dbConfig['host'] ?? 'localhost';
    $dbname = $dbConfig['dbname'] ?? 'datatime'; // Ajustez le nom de votre BDD si besoin
    $username = $dbConfig['user'] ?? 'root';
    $password = $dbConfig['password'] ?? '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $email = 'admin@game-catalog.local';
    $plainPassword = 'AdminPassword123!';
    
    // 🔐 Hachage indispensable pour que password_verify() fonctionne
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    $droit = 'admin';

    // 2. Vérifier si l'administrateur existe déjà
    $check = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = :email");
    $check->execute(['email' => $email]);

    if ($check->fetch()) {
        echo "⚠️ L'administrateur '{$email}' existe déjà en base de données.\n";
        exit;
    }

    // 3. Insertion de l'administrateur
    $sql = $pdo->prepare("INSERT INTO utilisateur (email, password, droit) VALUES (:email, :password, :droit)");
    $sql->execute([
        'email' => $email,
        'password' => $hashedPassword,
        'droit' => $droit
    ]);

    echo "✅ Administrateur créé avec succès !\n";
    echo "----------------------------------------\n";
    echo " Email : {$email}\n";
    echo " Mot de passe (en clair) : {$plainPassword}\n";
    echo " Rôle/Droit : {$droit}\n";
    echo "----------------------------------------\n";

} catch (\PDOException $e) {
    echo "❌ Erreur de base de données : " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}