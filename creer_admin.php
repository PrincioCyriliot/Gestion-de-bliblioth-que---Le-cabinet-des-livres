<?php
require_once 'db.php';

// Le mot de passe que l'on souhaite attribuer à l'admin
$pass_en_clair = 'admin123';

// Génération automatique du hash par PHP
$hash_correct = password_hash($pass_en_clair, PASSWORD_DEFAULT);

try {
    // 1. Supprimer l'ancien utilisateur s'il existe
    $pdo->exec("DELETE FROM utilisateurs WHERE username = 'admin'");

    // 2. Insérer le nouvel administrateur avec le hash propre
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (username, password) VALUES (:user, :pass)");
    $stmt->execute([
        ':user' => 'admin',
        ':pass' => $hash_correct
    ]);

    echo "<h2>Succès !</h2>";
    echo "<p>L'utilisateur <strong>admin</strong> a été réinséré avec succès.</p>";
    echo "<p>Mot de passe en clair : <strong>$pass_en_clair</strong></p>";
    echo "<p>Hash généré et stocké (longueur " . strlen($hash_correct) . " chars) :<br><code>" . $hash_correct . "</code></p>";
    echo "<br><a href='login.php'>👉 Aller à la page de connexion</a>";

} catch (PDOException $e) {
    echo "Erreur BDD : " . $e->getMessage();
}
