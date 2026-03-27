<?php
/**
 * Page de déconnexion
 * Détruit la session et redirige vers l'accueil
 */
require_once __DIR__ . '/../includes/session.php';

// Vider toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Retourner à l'accueil
redirect('../index.php');
