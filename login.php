<?php
session_start();// Démarre la gestion des sessions
require_once 'db.php';// Connexion à la BDD

// Si déjà connecté, redirection vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';// Variable pour stocker les messages d'erreur

// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $user_input = trim($_POST['username']);// Nettoie les espaces inutiles
    $password_input = $_POST['password'];// Mot de passe saisi
    

    if (!empty($user_input) && !empty($password_input)) {
        // Recherche de l'utilisateur dans MySQL
        // Sélectionne l'utilisateur correspondant dans la BDD
        $stmt =$pdo->prepare("SELECT * FROM utilisateurs WHERE username = :user");
        $stmt->execute([':user' => $user_input]);
        $user = $stmt->fetch();// Récupère le résultat

        // Compare le mot de passe saisi avec le hash stocké en base de données
        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user'] = $user['username'];// Sauvegarde de l'utilisateur dans la session
            header('Location: index.php');// Redirection
            exit;
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Connexion</title>
    <style>
        
        
    </style>
</head>
<body>
    <div class="card">
        <h2>Connexion</h2>
        <p>CRUD IFM</p>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="connexion">Se connecter</button>
        </form>
    </div>
</body>
</html>
