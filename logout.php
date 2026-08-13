<?php
session_start();// Démarre la session active
session_unset();// Vide toutes les variables de session
session_destroy();// Détruit complètement la session
header('Location: login.php');// Redirige vers la connexion
exit;// Stoppe l'exécution