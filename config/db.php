<?php
/**
 * Fichier de connexion à la base de données
 * On utilise PDO pour se connecter de façon sécurisée
 */

// --- Paramètres de connexion ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'prompt_library');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

/**
 * Crée et retourne une connexion PDO
 * Cette fonction est appelée dans chaque page qui a besoin de la BDD
 */
function getConnection() {
    // Le DSN (Data Source Name) contient les infos de connexion
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    // Options PDO : afficher les erreurs + sécurité renforcée
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Affiche les erreurs SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux associatifs
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive les fausses préparations
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // On arrête tout et on affiche l'erreur (en prod, on loguerait discrètement)
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
