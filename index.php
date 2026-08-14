<?php
session_start();

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
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (nom) VALUES (:nom)");
            $stmt->execute([':nom' => $nomCat]);
        }
    }

    // Ajouter un livre avec Upload d'image
    if ($_POST['action'] === 'ajouter_livre') {
        $titre = trim($_POST['titre']);
        $auteur = trim($_POST['auteur']);
        $id_categorie = (int)$_POST['id_categorie'];//(int) : Transtypage en entier pour des raisons de sécurité.
        $image_url = '';

        if (isset($_FILES['image_fichier']) && $_FILES['image_fichier']['error'] === UPLOAD_ERR_OK) {//UPLOAD_ERR_OK : Vérifie que l'image s'est bien téléchargée sur le serveur.
            $extension = strtolower(pathinfo($_FILES['image_fichier']['name'], PATHINFO_EXTENSION));//pathinfo(..., PATHINFO_EXTENSION) : Extrait l'extension du fichier (ex: .jpg).
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $extensions_autorisees)) {
                $nom_fichier = uniqid('img_') . '.' . $extension;//uniqid('img_') : Génère un identifiant unique basé sur le temps pour éviter que deux images de même nom ne se chevauchent ou s'écrasent.
                $dossier_destination = 'uploads/' . $nom_fichier;

                if (move_uploaded_file($_FILES['image_fichier']['tmp_name'], $dossier_destination)) {//move_uploaded_file() : Déplace l'image du dossier temporaire système vers le dossier uploads/ du projet.
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
                ':id_cat' => $id_categorie > 0 ? $id_categorie : null
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

        if (isset($_FILES['image_fichier']) && $_FILES['image_fichier']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['image_fichier']['name'], PATHINFO_EXTENSION));
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $extensions_autorisees)) {
                $nom_fichier = uniqid('img_') . '.' . $extension;
                $dossier_destination = 'uploads/' . $nom_fichier;

                if (move_uploaded_file($_FILES['image_fichier']['tmp_name'], $dossier_destination)) {
                    if (!empty($image_url) && file_exists($image_url)) {
                        unlink($image_url);//unlink($image_url) : Supprime le fichier image obsolète du serveur pour ne pas encombrer le disque dur inutiles.
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
            $stmt = $pdo->prepare("SELECT image_url FROM livres WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $livre = $stmt->fetch();

            if ($livre && !empty($livre['image_url']) && file_exists($livre['image_url'])) {
                unlink($livre['image_url']);
            }

            $stmt = $pdo->prepare("DELETE FROM livres WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
    }

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

if (!empty($recherche)) {
    $sql .= " AND (livres.titre LIKE :q OR livres.auteur LIKE :q)";
    $params[':q'] = '%' . $recherche . '%';
}

if ($catFiltre > 0) {
    $sql .= " AND livres.id_categorie = :cat";
    $params[':cat'] = $catFiltre;
}

$sql .= " ORDER BY livres.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

$livreAEditer = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit_id']]);
    $livreAEditer = $stmt->fetch();
}

require_once 'view.php';
