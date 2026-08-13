<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view.css">
    <title>Gestion de Bibliothèque</title>
    <style>
       
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>📚 Bibliothèque CRUD</h1>
        <div>
            <span>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
            <a href="logout.php" class="btn-logout" style="margin-left: 1rem;">Déconnexion</a>
        </div>
    </header>

    <div class="grid">
        <div>
            <!-- Formulaire Livre avec upload d'image -->
            <div class="card">
                <h2><?= $livreAEditer ? 'Modifier le livre' : 'Ajouter un livre' ?></h2>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                    <input type="hidden" name="action" value="<?= $livreAEditer ? 'modifier_livre' : 'ajouter_livre' ?>">
                    <?php if ($livreAEditer): ?>
                        <input type="hidden" name="id" value="<?= $livreAEditer['id'] ?>">
                        <input type="hidden" name="ancienne_image" value="<?= htmlspecialchars($livreAEditer['image_url']) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Titre</label>
                        <input type="text" name="titre" value="<?= $livreAEditer ? htmlspecialchars($livreAEditer['titre']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Auteur</label>
                        <input type="text" name="auteur" value="<?= $livreAEditer ? htmlspecialchars($livreAEditer['auteur']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="id_categorie">  
                            <option value="0">-- Aucune catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($livreAEditer && $livreAEditer['id_categorie'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Photo de couverture</label>
                        <input type="file" name="image_fichier" accept="image/*">
                        <?php if ($livreAEditer && !empty($livreAEditer['image_url'])): ?>
                            <small style="color: #cbd5e1; margin-top: 4px;">Image actuelle conservée</small>
                        <?php endif; ?>
                    </div>

                    <button type="submit"><?= $livreAEditer ? 'Enregistrer les modifications' : 'Ajouter le livre' ?></button>
                    <?php if ($livreAEditer): ?>
                        <a href="index.php" style="display: block; text-align: center; margin-top: 0.5rem; color: #94a3b8; font-size: 0.85rem;">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Formulaire Catégorie -->
            <div class="card">
                <h2>Ajouter une catégorie</h2>
                <form method="POST" style="margin-top: 1rem;">
                    <input type="hidden" name="action" value="ajouter_categorie">
                    <div class="form-group">
                        <label>Nom de la catégorie</label>
                        <input type="text" name="nom_categorie" placeholder="Ex: Roman, Sci-Fi..." required>
                    </div>
                    <button type="submit" style="background: #10b981;">Ajouter la catégorie</button>
                </form>
            </div>
        </div>

        <div>
            <!-- Recherche & Filtrage -->
            <form method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Rechercher par titre ou auteur..." value="<?= htmlspecialchars($recherche) ?>">
                <select name="cat" onchange="this.form.submit()">
                    <option value="0">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $catFiltre == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" style="width: auto;">Filtrer</button>
            </form>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>Couverture</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Catégorie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($livres)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">Aucun livre trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($livres as $l): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($l['image_url']) && file_exists($l['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($l['image_url']) ?>" alt="Couverture" class="img-thumb">
                                    <?php else: ?>
                                        <div class="img-thumb" style="display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#64748b;">Pas d'image</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($l['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($l['auteur']) ?></td>
                                <td>
                                    <?php if ($l['categorie_nom']): ?>
                                        <span class="badge"><?= htmlspecialchars($l['categorie_nom']) ?></span>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 0.85rem;">Non classé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="index.php?edit_id=<?= $l['id'] ?>" class="btn-edit">Modifier</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce livre ?');">
                                            <input type="hidden" name="action" value="supprimer_livre">
                                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                            <button type="submit" class="btn-delete">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>