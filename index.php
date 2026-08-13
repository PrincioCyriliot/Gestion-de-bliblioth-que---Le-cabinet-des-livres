<?php
session_start();

// Protection de l'accès : redirige vers le login si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

// =========================================================
// 1. GESTION DES ACTIONS (POST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Ajouter une catégorie
    if ($_POST['action'] === 'ajouter_categorie') {
        $nomCat = trim($_POST['nom_categorie']);
        if (!empty($nomCat)) {
            // INSERT IGNORE évite les erreurs si le nom existe déjà
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (nom) VALUES (:nom)");
            $stmt->execute([':nom' => $nomCat]);
        }
    }

    // Ajouter un livre avec Upload d'image
    if ($_POST['action'] === 'ajouter_livre') {
        $titre = trim($_POST['titre']);
        $auteur = trim($_POST['auteur']);
        $id_categorie = (int)$_POST['id_categorie'];
        $image_url = '';
    // Traitement du fichier uploadé
        if (isset($_FILES['image_fichier']) && $_FILES['image_fichier']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['image_fichier']['name'], PATHINFO_EXTENSION));
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        // Donne un nom unique au fichier pour éviter les collisions
            if (in_array($extension, $extensions_autorisees)) {
                $nom_fichier = uniqid('img_') . '.' . $extension;
                $dossier_destination = 'uploads/' . $nom_fichier;
        // Déplace le fichier temporaire vers le dossier permanent
                if (move_uploaded_file($_FILES['image_fichier']['tmp_name'], $dossier_destination)) {
                    $image_url = $dossier_destination;
                }
            }
        }
   
        if (!empty($titre) && !empty($auteur)) {
            $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, image_url, id_categorie) VALUES (:titre, :auteur, :image_url, :id_cat)");
            $stmt->execute([
                ':titre' => $titre,
                ':auteur' => $auteur,
                ':image_url' => $image_url,
                ':id_cat' => $id_categorie > 0 ? $id_categorie : null // Gestion de l'option "sans catégorie"[
            ]);
        }
    }

    // Modifier un livre
    if ($_POST['action'] === 'modifier_livre') {
        $id = (int)$_POST['id'];
        $titre = trim($_POST['titre']);
        $auteur = trim($_POST['auteur']);
        $id_categorie = (int)$_POST['id_categorie'];
        $image_url = $_POST['ancienne_image'] ?? '';
    // Remplacement de l'image si un nouveau fichier est envoy
        if (isset($_FILES['image_fichier']) && $_FILES['image_fichier']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['image_fichier']['name'], PATHINFO_EXTENSION));
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $extensions_autorisees)) {
                $nom_fichier = uniqid('img_') . '.' . $extension;
                $dossier_destination = 'uploads/' . $nom_fichier;

                if (move_uploaded_file($_FILES['image_fichier']['tmp_name'], $dossier_destination)) {
                // Supprime l'ancien fichier sur le disque s'il existe   
                if (!empty($image_url) && file_exists($image_url)) {
                        unlink($image_url);
                    }
                    $image_url = $dossier_destination;
                }
            }
        }

        if ($id > 0 && !empty($titre) && !empty($auteur)) {
            $stmt = $pdo->prepare("UPDATE livres SET titre = :titre, auteur = :auteur, image_url = :image_url, id_categorie = :id_cat WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':titre' => $titre,
                ':auteur' => $auteur,
                ':image_url' => $image_url,
                ':id_cat' => $id_categorie > 0 ? $id_categorie : null
            ]);
        }
    }

    // Supprimer un livre et son fichier image sur le serveur
    if ($_POST['action'] === 'supprimer_livre') {
        $id = (int)$_POST['id'];
        if ($id > 0) {
            // Récupère l'image pour la supprimer du disque
            $stmt = $pdo->prepare("SELECT image_url FROM livres WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $livre = $stmt->fetch();

            if ($livre && !empty($livre['image_url']) && file_exists($livre['image_url'])) {
                unlink($livre['image_url']);// Suppression physiquement du fichier
            }
        // Suppression en BDD
            $stmt = $pdo->prepare("DELETE FROM livres WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
    }
    // Pattern Post/Redirect/Get (PRG) pour éviter les re-soumissions de formulaires au rafraîchissement
    header('Location: index.php');
    exit;
}

// =========================================================
// 2. RECHERCHE ET FILTRAGE (GET)
// =========================================================
$recherche = isset($_GET['q']) ? trim($_GET['q']) : '';
$catFiltre = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$sql = "SELECT livres.*, categories.nom AS categorie_nom 
        FROM livres 
        LEFT JOIN categories ON livres.id_categorie = categories.id 
        WHERE 1=1";

$params = [];
// Filtre texte
if (!empty($recherche)) {
    $sql .= " AND (livres.titre LIKE :q OR livres.auteur LIKE :q)";
    $params[':q'] = '%' . $recherche . '%';
}
// Filtre par catégorie
if ($catFiltre > 0) {
    $sql .= " AND livres.id_categorie = :cat";
    $params[':cat'] = $catFiltre;
}

$sql .= " ORDER BY livres.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

// Récupération des catégories pour la liste déroulante
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// Si un livre est en cours d'édition (clic sur "Modifier")
$livreAEditer = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit_id']]);
    $livreAEditer = $stmt->fetch();
}
// Charge le fichier de présentation HTML
require_once 'view.php';