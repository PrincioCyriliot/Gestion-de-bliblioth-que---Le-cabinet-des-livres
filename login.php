<?php
session_start();
require_once 'db.php';

// Si déjà connecté, redirection vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $user_input = trim($_POST['username']);
    $password_input = $_POST['password'];

    if (!empty($user_input) && !empty($password_input)) {

        $stmt = $pdo->prepare(
            "SELECT * FROM utilisateurs WHERE username = :user"
        );

        $stmt->execute([
            ':user' => $user_input
        ]);

        $user = $stmt->fetch();

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user'] = $user['username'];

            header('Location: index.php');
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

    <title>Le Cabinet des Livres — Connexion</title>
</head>

<body>

<div class="login-page">

    <div class="ambient ambient-one"></div>
    <div class="ambient ambient-two"></div>

    <main class="login-card">

        <!-- Décoration supérieure -->
        <div class="crest">
            <span class="crest-line"></span>
            <span class="crest-symbol">✦</span>
            <span class="crest-line"></span>
        </div>


        <!-- Logo / nom de la bibliothèque -->
        <div class="brand">

            <div class="brand-small">
                BIBLIOTHÈQUE PRIVÉE
            </div>

            <h1>
                Le Cabinet<br>
                <em>des Livres</em>
            </h1>

            <p>
                Votre collection, votre histoire.
            </p>

        </div>


        <!-- Séparateur -->
        <div class="ornament">

            <span></span>

            <i>◆</i>

            <span></span>

        </div>


        <!-- Connexion -->
        <section class="login-form">

            <div class="welcome">

                <h2>Bienvenue</h2>

                <p>
                    Ouvrez les portes de votre bibliothèque.
                </p>

            </div>


            <!-- Message d'erreur -->
            <?php if (!empty($erreur)): ?>

                <div class="error-message">

                    <span>!</span>

                    <div>
                        <?= htmlspecialchars($erreur) ?>
                    </div>

                </div>

            <?php endif; ?>


            <form method="POST">

                <!-- Identifiant -->
                <div class="field">

                    <label for="username">
                        Identifiant
                    </label>

                    <div class="input-wrap">

                        <span class="input-icon">
                            ♙
                        </span>

                        <input
                            id="username"
                            type="text"
                            name="username"
                            placeholder="Votre identifiant"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <!-- Mot de passe -->
                <div class="field">

                    <label for="password">
                        Mot de passe
                    </label>

                    <div class="input-wrap">

                        <span class="input-icon">
                            ▣
                        </span>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Votre mot de passe"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            aria-label="Afficher le mot de passe"
                            onclick="togglePassword()"
                        >
                            ◉
                        </button>

                    </div>

                </div>


                <!-- Bouton -->
                <button
                    type="submit"
                    name="connexion"
                    class="login-button"
                >

                    <span>
                        Entrer dans la bibliothèque
                    </span>

                    <b>→</b>

                </button>

            </form>

        </section>


        <!-- Décoration inférieure -->
        <div class="bottom-ornament">

            <span>✧</span>

            <p>
                COLLECTION &nbsp; • &nbsp;
                SAVOIR &nbsp; • &nbsp;
                ÉVASION
            </p>

            <span>✧</span>

        </div>

    </main>


    <footer>
        LE CABINET DES LIVRES
        &nbsp; · &nbsp;
        COLLECTION PRIVÉE
    </footer>

</div>


<script>

function togglePassword() {

    const input = document.getElementById("password");

    const button = document.querySelector(".toggle-password");


    if (input.type === "password") {

        input.type = "text";

        button.textContent = "◌";

        button.setAttribute(
            "aria-label",
            "Masquer le mot de passe"
        );

    } else {

        input.type = "password";

        button.textContent = "◉";

        button.setAttribute(
            "aria-label",
            "Afficher le mot de passe"
        );

    }

}

</script>

</body>

</html>
