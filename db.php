<?php
$host = 'localhost'; // L'adresse du serveur SQL (en local)[cite: 2]
$dbname = 'ma_bibliotheque'; // Le nom de la base de données cible[cite: 2]
$username = 'root'; // Nom d'utilisateur par défaut sous XAMPP/WAMP[cite: 2]
$password = ''; // Mot de passe (vide par défaut en local)[cite: 2]

try {
    // Crée une instance PDO pour connecter PHP à MySQL[cite: 2]
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configure PDO pour qu'il lève une exception (erreur) en cas de problème SQL[cite: 2]
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Définit le mode de récupération par défaut sous forme de tableau associatif[cite: 2]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la connexion échoue, stoppe le script et affiche le message d'erreur[cite: 2]
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}