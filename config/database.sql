-- ============================================================
-- Script de création de la base de données
-- À exécuter une seule fois dans phpMyAdmin ou MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS prompt_library CHARACTER SET utf8 COLLATE utf8_general_ci;
USE prompt_library;

-- ------------------------------------------------------------
-- Table 1 : users (les comptes utilisateurs)
-- ------------------------------------------------------------
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,           -- Nom d'utilisateur unique
    email      VARCHAR(100) NOT NULL UNIQUE,           -- Email unique
    password   VARCHAR(255) NOT NULL,                  -- Mot de passe hashé
    role       ENUM('user', 'admin') DEFAULT 'user',   -- Rôle : user ou admin
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table 2 : categories (les thématiques des prompts)
-- ------------------------------------------------------------
CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50) NOT NULL UNIQUE,            -- Ex: Code, Marketing, DevOps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table 3 : prompts (les prompts sauvegardés)
-- La colonne user_id pointe vers users.id (Foreign Key)
-- La colonne category_id pointe vers categories.id (Foreign Key)
-- ------------------------------------------------------------
CREATE TABLE prompts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,                 -- Titre du prompt
    content     TEXT         NOT NULL,                 -- Le texte du prompt
    user_id     INT          NOT NULL,                 -- Auteur
    category_id INT          NOT NULL,                 -- Thématique
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Foreign Keys : garantit l'intégrité des données
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Données de départ (seed)
-- ------------------------------------------------------------

-- Un compte admin par défaut (mot de passe : admin123)
INSERT INTO users (username, email, password, role) VALUES (
    'admin',
    'admin@devgenius.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'admin'
);

-- Catégories de départ
INSERT INTO categories (name) VALUES
    ('Code'),
    ('Marketing'),
    ('DevOps'),
    ('SQL'),
    ('Documentation'),
    ('Tests');
