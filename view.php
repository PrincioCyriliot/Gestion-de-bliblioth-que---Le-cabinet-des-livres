<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="view.css">
    <title>Le Cabinet des Livres</title>
</head>
<body>

<div class="page-shell">

    <header class="topbar">
        <div class="brand">
            <div class="brand-mark">✦</div>
            <div>
                <div class="eyebrow">COLLECTION PRIVÉE</div>
                <h1>Le Cabinet des Livres</h1>
            </div>
        </div>

        <div class="account">
            <div class="user-chip">
                <span class="user-dot">●</span>
                <span><?= htmlspecialchars($_SESSION['user']) ?></span>
            </div>
            <a href="logout.php" class="btn-logout">Quitter</a>
        </div>
    </header>

    <section class="hero">
        <div>
            <span class="hero-kicker">CATALOGUE & COLLECTIONS</span>
            <h2>Chaque livre a sa place.</h2>
            <p>Organisez votre bibliothèque, retrouvez vos ouvrages et construisez une collection qui vous ressemble.</p>
        </div>
        <div class="hero-badge">
            <span>✧</span>

            <strong><?= count( $livres) ?></strong>
            <small>ouvrage<?= count($livres) > 1 ? 's' : '' ?> affiché<?= count($livres) > 1 ? 's' : '' ?></small>
        </div>
    </section>

    <div class="layout">

        <aside class="sidebar">
            <div class="panel">
                <div class="panel-title">
                    <span class="mini-icon">＋</span>
                    <div>
                        <small>COLLECTION</small>
                        <h3><?= $livreAEditer ? 'Modifier un ouvrage' : 'Ajouter un ouvrage' ?></h3>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $livreAEditer ? 'modifier_livre' : 'ajouter_livre' ?>">
                    <?php if ($livreAEditer): ?>
                        <input type="hidden" name="id" value="<?= $livreAEditer['id'] ?>">
                        <input type="hidden" name="ancienne_image" value="<?= htmlspecialchars($livreAEditer['image_url']) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Titre du livre</label>
                        <input type="text" name="titre" placeholder="Ex. Le Petit Prince"
                               value="<?= $livreAEditer ? htmlspecialchars($livreAEditer['titre']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Auteur</label>
                        <input type="text" name="auteur" placeholder="Nom de l'auteur"
                               value="<?= $livreAEditer ? htmlspecialchars($livreAEditer['auteur']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Rayon / catégorie</label>
                        <select name="id_categorie">
                            <option value="0">Sans catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($livreAEditer && $livreAEditer['id_categorie'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Couverture</label>
                        <label class="file-drop">
                            <span class="file-icon">▧</span>
                            <span>Choisir une image</span>
                            <input type="file" name="image_fichier" accept="image/*">
                        </label>
                        <?php if ($livreAEditer && !empty($livreAEditer['image_url'])): ?>
                            <small class="hint">La couverture actuelle sera conservée si aucune nouvelle image n'est choisie.</small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-primary">
                        <?= $livreAEditer ? 'Enregistrer les changements' : 'Ajouter à la collection' ?>
                    </button>

                    <?php if ($livreAEditer): ?>
                        <a href="index.php" class="cancel-link">Annuler la modification</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="panel category-panel">
                <div class="panel-title">
                    <span class="mini-icon">◇</span>
                    <div>
                        <small>ORGANISATION</small>
                        <h3>Créer un rayon</h3>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="ajouter_categorie">
                    <div class="form-group">
                        <label>Nom de la catégorie</label>
                        <input type="text" name="nom_categorie" placeholder="Roman, Fantaisie..." required>
                    </div>
                    <button type="submit" class="btn-secondary">Créer la catégorie</button>
                </form>

                <?php if (!empty($categories)): ?>
                    <div class="category-list">
                        <?php foreach ($categories as $cat): ?>
                            <span><?= htmlspecialchars($cat['nom']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <main class="library">
            <form method="GET" class="search-panel">
                <div class="search-input">
                    <span>⌕</span>
                    <input type="text" name="q" placeholder="Rechercher un titre ou un auteur..."
                           value="<?= htmlspecialchars($recherche) ?>">
                </div>

                <select name="cat" class="filter-select">
                    <option value="0">Tous les rayons</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $catFiltre == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="search-button">Rechercher</button>
            </form>

            <div class="section-heading">
                <div>
                    <span class="section-kicker">BIBLIOTHÈQUE</span>
                    <h2>Les ouvrages de la collection</h2>
                </div>
                <span class="result-count"><?= count($livres) ?> résultat<?= count($livres) > 1 ? 's' : '' ?></span>
            </div>

            <?php if (empty($livres)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📖</div>
                    <h3>Aucun ouvrage trouvé</h3>
                    <p>Essayez une autre recherche ou ajoutez un nouveau livre à votre collection.</p>
                </div>
            <?php else: ?>
                <div class="bookshelf">
                    <?php foreach ($livres as $l): ?>
                        <article class="book-card">
                            <div class="book-cover-wrap">
                                <?php if (!empty($l['image_url']) && file_exists($l['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($l['image_url']) ?>" alt="Couverture de <?= htmlspecialchars($l['titre']) ?>" class="book-cover">
                                <?php else: ?>
                                    <div class="no-cover">
                                        <span>✦</span>
                                        <small>Sans couverture</small>
                                    </div>
                                <?php endif; ?>

                                <?php if ($l['categorie_nom']): ?>
                                    <span class="book-category"><?= htmlspecialchars($l['categorie_nom']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="book-info">
                                <h3><?= htmlspecialchars($l['titre']) ?></h3>
                                <p class="author">par <?= htmlspecialchars($l['auteur']) ?></p>

                                <div class="book-actions">
                                    <a href="index.php?edit_id=<?= $l['id'] ?>" class="edit-link">Modifier</a>
                                    <form method="POST" onsubmit="return confirm('Supprimer ce livre ?');">
                                        <input type="hidden" name="action" value="supprimer_livre">
                                        <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                        <button type="submit" class="delete-link">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <span>✦ Le Cabinet des Livres</span>
        <span>Gestion de collection</span>
    </footer>
</div>

</body>
</html>
