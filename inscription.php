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



/*
<?php
require_once 'db.php'; // Inclut la connexion PDO à MySQL

// 1. Un seul tableau PHP qui contient TOUS tes utilisateurs et leurs mots de passe
// Structure : [ 'nom_utilisateur' => 'mot_de_passe_en_clair' ]
$utilisateurs = [
    'admin'    => 'admin123',
    'ryan'     => 'CodeSecurise2026',
    'elise'    => 'MonPasse123!',
    'supervis' => 'AdminSysteme99'
];

// 2. Requête SQL d'insertion qui met aussi à jour le mot de passe s'il existe déjà[cite: 1, 4, 8]
$sql = "INSERT INTO utilisateurs (username, password) 
        VALUES (:user, :pass) 
        ON DUPLICATE KEY UPDATE password = :pass";[cite: 1, 4, 8]

$stmt = $pdo->prepare($sql);[cite: 1]

// 3. La boucle qui parcourt chaque utilisateur du tableau PHP
foreach ($utilisateurs as $pseudo => $mdp_clair) {
    
    // Génération automatique du hash sécurisé pour cet utilisateur
    $hash = password_hash($mdp_clair, PASSWORD_DEFAULT);[cite: 1, 4]

    // Exécution de la requête pour chaque tour de boucle[cite: 1]
    $stmt->execute([
        ':user' => $pseudo,[cite: 1]
        ':pass' => $hash[cite: 1]
    ]);

    echo "Utilisateur <strong>" . htmlspecialchars($pseudo) . "</strong> enregistré / mis à jour avec son hash !<br>";
}

echo "<br><a href='login.php'>👉 Tester la connexion</a>";

*/