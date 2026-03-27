<?php
/**
 * Fonctions utilitaires pour la session et la sécurité
 * Ce fichier est inclus dans toutes les pages
 */

// Démarre la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 * Retourne true ou false
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur connecté est admin
 * Retourne true ou false
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirige vers une page et arrête le script
 * Exemple : redirect('pages/login.php')
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Protège une page : si non connecté, redirige vers login
 * À appeler en haut des pages protégées
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('../pages/login.php');
    }
}

/**
 * Protège une page admin : si non admin, redirige vers accueil
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('../index.php');
    }
}

/**
 * Nettoie une donnée envoyée par l'utilisateur
 * Évite les injections HTML/JS (XSS)
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
