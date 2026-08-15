# 📚 Le Cabinet des Livres

> **Application web PHP / MySQL de gestion de bibliothèque privée**  
> Une interface élégante pour organiser votre collection de livres, vos rayons et vos couvertures d'ouvrages.

---

## 🌟 Fonctionnalités

* **Authentification sécurisée** : Connexion/Déconnexion avec hachage de mot de passe (`password_hash` / `password_verify`).
* **Gestion des livres (CRUD)** :
  * **Ajout** : Titre, auteur, rayon et téléversement d'image de couverture.
  * **Recherche & Filtrage** : Recherche instantanée par mot-clé (titre/auteur) et filtre par rayon.
  * **Modification** : Mise à jour des informations et remplacement/suppression automatique des anciennes images sur le serveur.
  * **Suppression** : Retrait du livre et nettoyage du fichier image associé (`uploads/`).
* **Gestion des rayons (Catégories)** : Création dynamique de catégories liées par clés étrangères SQL.
* **Interface soignée** : Thème vintage et responsive.

---

## 📁 Structure du Projet

```text
├── uploads/            # Dossier de stockage des images envoyées
├── db.php              # Connexion PDO à la base de données MySQL
├── schema.sql          # Script SQL de création des tables et données de test
├── creer_admin.php     # Script de secours pour réinitialiser l'administrateur,effectue le hachage du mot de passe
├── index.php           # Logique métier (CRUD, requêtes GET/POST, sessions)
├── view.php            # Vue HTML principale de la bibliothèque
├── login.php           # Traitement et vue de la page de connexion(Sécurisation hachage sel & poivre)
├── logout.php          # Déconnexion
├── view.css            # Styles de l'interface principale
└── login.css           # Styles de la page de connexion
