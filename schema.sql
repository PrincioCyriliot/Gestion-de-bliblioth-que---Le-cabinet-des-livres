-- 1. Création de la base de données (si tu utilises MySQL / MariaDB)
CREATE DATABASE IF NOT EXISTS ma_bibliotheque;
USE ma_bibliotheque;

-- 2. Table des utilisateurs (pour le système de connexion)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table des catégories (pour le classement)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
);

-- 4. Table des livres (avec clé étrangère et lien d'image)
CREATE TABLE IF NOT EXISTS livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    image_url TEXT,
    id_categorie INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_livres_categories 
        FOREIGN KEY (id_categorie) 
        REFERENCES categories(id) 
        ON DELETE SET NULL
);

-- =========================================================
-- JEU DE DONNÉES PAR DÉFAUT (DONNÉES DE TEST)
-- =========================================================

-- Insertion des catégories initiales
INSERT INTO categories (nom) VALUES 
('Fantasy'),
('Science-Fiction'),
('Roman'),
('Informatique');

-- Insertion d'un utilisateur administrateur
-- Remarque : Le mot de passe ici correspond au hachage de 'admin123'
INSERT INTO utilisateurs (username, password) VALUES 
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8cQ0z8KxI1M1X.y6n0p0PZ8Qz8KxI');

-- Insertion de quelques livres exemples
INSERT INTO livres (titre, auteur, image_url, id_categorie) VALUES 
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c', 1),
('Dune', 'Frank Herbert', 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e', 2),
('Apprendre PHP & MySQL', 'Mark Myers', 'https://images.unsplash.com/photo-1532012197267-da84d127e765', 4);
