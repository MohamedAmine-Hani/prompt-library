<?php
/**
 * Page d'accueil
 * Affiche tous les prompts avec un filtre par catégorie
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/db.php';

$pdo = getConnection();

// --- Récupérer toutes les catégories (pour le menu de filtre) ---
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// --- Filtre : si une catégorie est choisie dans l'URL (?category_id=2) ---
$filterCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// --- Requête pour récupérer les prompts ---
// On joint les tables pour avoir le nom de l'auteur et de la catégorie
if ($filterCategoryId > 0) {
    // Avec filtre
    $stmt = $pdo->prepare("
        SELECT prompts.*, users.username, categories.name AS category_name
        FROM prompts
        JOIN users      ON prompts.user_id     = users.id
        JOIN categories ON prompts.category_id = categories.id
        WHERE prompts.category_id = ?
        ORDER BY prompts.created_at DESC
    ");
    $stmt->execute([$filterCategoryId]);
} else {
    // Sans filtre : tous les prompts
    $stmt = $pdo->query("
        SELECT prompts.*, users.username, categories.name AS category_name
        FROM prompts
        JOIN users      ON prompts.user_id     = users.id
        JOIN categories ON prompts.category_id = categories.id
        ORDER BY prompts.created_at DESC
    ");
}

$prompts = $stmt->fetchAll();
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">

    <!-- Titre de la page -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 1.8rem; color: #a78bfa;">📚 Bibliothèque de Prompts</h1>
        <p style="color: #777; margin-top: 8px;">
            <?= count($prompts) ?> prompt(s) disponible(s)
            <?php if (!isLoggedIn()): ?>
                — <a href="pages/register.php">Créez un compte</a> pour contribuer
            <?php endif; ?>
        </p>
    </div>

    <!-- Filtres par catégorie -->
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px;">
        <a href="index.php"
           class="badge"
           style="padding: 7px 16px; font-size:0.85rem; <?= $filterCategoryId === 0 ? 'background:#7c6af0; color:white;' : '' ?>">
            Tous
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?category_id=<?= $cat['id'] ?>"
               class="badge"
               style="padding: 7px 16px; font-size:0.85rem; <?= $filterCategoryId === (int)$cat['id'] ? 'background:#7c6af0; color:white;' : '' ?>">
                <?= clean($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
        <?php if (isLoggedIn()): ?>
    <div style="margin-bottom: 24px;">
        <a href="pages/add_prompt.php" class="btn btn-primary">
            + Ajouter un prompt
        </a>
    </div>
    <?php endif; ?>

    <!-- Grille de prompts -->
    <?php if (empty($prompts)): ?>
        <div class="card" style="text-align:center; color:#777;">
            <p>Aucun prompt pour l'instant. Soyez le premier à contribuer ! 🚀</p>
        </div>
    <?php else: ?>
        <div class="prompts-grid">
            <?php foreach ($prompts as $prompt): ?>
                <div class="prompt-card">
                    <span class="badge"><?= clean($prompt['category_name']) ?></span>
                    <h3 style="margin-top: 10px;"><?= clean($prompt['title']) ?></h3>
                    <p class="preview"><?= clean($prompt['content']) ?></p>
                    <div class="meta">
                        👤 <?= clean($prompt['username']) ?>
                        &nbsp;·&nbsp;
                        🕐 <?= date('d/m/Y', strtotime($prompt['created_at'])) ?>
                    </div>
                    <!-- Bouton pour voir le prompt complet -->
                    <a href="pages/view_prompt.php?id=<?= $prompt['id'] ?>"
                       style="display:block; margin-top:14px; text-align:center;"
                       class="btn btn-primary btn-sm">
                        Voir le prompt
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
