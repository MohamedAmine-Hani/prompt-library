<?php
/**
 * En-tête HTML commun à toutes les pages
 * Contient le menu de navigation
 */
require_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptLibrary – DevGenius Solutions</title>
    <style>
        /* ---- Reset & Base ---- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        a { color: #7c6af0; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* ---- Navbar ---- */
        nav {
            background: #1a1a2e;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #2a2a4a;
        }
        nav .logo { font-size: 1.3rem; font-weight: bold; color: #7c6af0; }
        nav .logo span { color: #a78bfa; }
        nav ul { list-style: none; display: flex; gap: 20px; align-items: center; }
        nav ul li a { color: #ccc; font-size: 0.9rem; }
        nav ul li a:hover { color: #a78bfa; text-decoration: none; }
        nav .btn-nav {
            background: #7c6af0; color: white; padding: 7px 16px;
            border-radius: 6px; font-size: 0.85rem;
        }
        nav .btn-nav:hover { background: #6b5ce0; text-decoration: none; }

        /* ---- Contenu principal ---- */
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        /* ---- Carte ---- */
        .card {
            background: #1a1a2e; border: 1px solid #2a2a4a;
            border-radius: 12px; padding: 30px; margin-bottom: 20px;
        }
        .card h2 { font-size: 1.2rem; color: #a78bfa; margin-bottom: 20px; }

        /* ---- Formulaires ---- */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.85rem; color: #aaa; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px 14px; background: #0f0f1a;
            border: 1px solid #3a3a5a; border-radius: 8px; color: #e0e0e0;
            font-size: 0.95rem;
        }
        .form-group textarea { height: 120px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none; border-color: #7c6af0;
        }

        /* ---- Boutons ---- */
        .btn {
            display: inline-block; padding: 10px 22px; border-radius: 8px;
            border: none; cursor: pointer; font-size: 0.95rem; font-weight: 600;
        }
        .btn-primary { background: #7c6af0; color: white; }
        .btn-primary:hover { background: #6b5ce0; }
        .btn-danger { background: #e05c5c; color: white; }
        .btn-danger:hover { background: #c44; }
        .btn-sm { padding: 5px 12px; font-size: 0.8rem; }

        /* ---- Alertes ---- */
        .alert {
            padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;
        }
        .alert-error   { background: #3a1a1a; border: 1px solid #e05c5c; color: #f5a5a5; }
        .alert-success { background: #1a3a2a; border: 1px solid #5ce08a; color: #a5f5c5; }

        /* ---- Badge catégorie ---- */
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 600; background: #2a2a4a; color: #a78bfa;
        }

        /* ---- Grille de prompts ---- */
        .prompts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
        .prompt-card {
            background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 12px;
            padding: 20px; transition: border-color 0.2s;
        }
        .prompt-card:hover { border-color: #7c6af0; }
        .prompt-card h3 { font-size: 1rem; margin-bottom: 8px; color: #e0e0e0; }
        .prompt-card .preview {
            font-size: 0.85rem; color: #888; margin: 10px 0;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .prompt-card .meta { font-size: 0.78rem; color: #666; margin-top: 10px; }
    </style>
</head>
<body>
<nav>
    <div class="logo">Prompt<span>Library</span></div>
    <ul>
        <li><a href="/prompt-library/index.php">Accueil</a></li>
        <?php if (isLoggedIn()): ?>
            <li><a href="/prompt-library/pages/add_prompt.php">+ Ajouter</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="/prompt-library/pages/admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="/prompt-library/pages/logout.php" class="btn-nav">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="/prompt-library/pages/login.php">Connexion</a></li>
            <li><a href="/prompt-library/pages/register.php" class="btn-nav">S'inscrire</a></li>
        <?php endif; ?>
    </ul>
</nav>
