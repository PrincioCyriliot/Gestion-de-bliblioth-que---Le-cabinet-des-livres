<?php
require_once 'db.php'; // Inclut le fichier de connexion à la BDD

// Le mot de passe brute que l'on souhaite attribuer à l'admin
$mot_de_passe = 'princio';
//Systeme "sel + poivre"

// Génération automatique du hash par PHP
$hash_correct = password_hash($mot_de_passe, PASSWORD_DEFAULT);// Génère un hash Bcrypt sécurisé

try {
    // 1. Supprimer l'ancien utilisateur s'il existe
    $pdo->exec("DELETE FROM utilisateurs WHERE username = 'admin'");

    // 2. Insérer le nouvel administrateur avec le hash propre
    // Prépare la requête SQL d'insertion sécurisée contre les injections SQL
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (username, password) VALUES (:user, :pass)");
    $stmt->execute([
        ':user' => 'admin',
        ':pass' => $hash_correct
    ]);
    // Messages de confirmation d'exécution
    echo "<h2>Succès !</h2>";
    echo "<p>L'utilisateur <strong>$mot_de_passe</strong> a été réinséré avec succès.</p>";
    echo "<p>Mot de passe en clair : <strong>:p</strong></p> $mot_de_passe";
    echo "<p>Hash généré et stocké (longueur " . strlen($hash_correct) . " chars) :<br><code>" . $hash_correct . "</code></p>";
    echo "<br><a href='login.php'>👉 Aller à la page de connexion</a>";

} catch (PDOException $e) {
    echo "Erreur BDD : " . $e->getMessage();
}