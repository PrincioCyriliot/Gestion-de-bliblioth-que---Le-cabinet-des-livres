<?php
require_once 'db.php';

// Le mot de passe qu'on veut utiliser
$pass_en_clair = 'admin123';

// PHP génère le hash parfait adapté à ta version de PHP
$pass_hache = password_hash($pass_en_clair, PASSWORD_DEFAULT);

// Mise à jour dans la BDD
$stmt = $pdo->prepare("UPDATE utilisateurs SET password = :pass WHERE username = 'admin'");
$stmt->execute([':pass' => $pass_hache]);

echo "Mot de passe mis à jour avec le hash : " . htmlspecialchars($pass_hache);
