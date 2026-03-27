<?php
/**
 * Page de détail d'un prompt
 * Affiche le contenu complet avec option de suppression (auteur ou admin)
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

// Récupérer l'ID dans l'URL (?id=5)
$promptId = (int)($_GET['id'] ?? 0);

if ($promptId === 0) {
    redirect('../index.php');
}

// Récupérer le prompt avec le nom de l'auteur et de la catégorie
$stmt = $pdo->prepare("
    SELECT prompts.*, users.username, categories.name AS category_name
    FROM prompts
    JOIN users      ON prompts.user_id     = users.id
    JOIN categories ON prompts.category_id = categories.id
    WHERE prompts.id = ?
");
$stmt->execute([$promptId]);
$prompt = $stmt->fetch();

// Si le prompt n'existe pas, retourner à l'accueil
if (!$prompt) {
    redirect('../index.php');
}

// --- Suppression du prompt ---
// Seul l'auteur ou un admin peut supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (isLoggedIn() && ($_SESSION['user_id'] === (int)$prompt['user_id'] || isAdmin())) {
        $stmt = $pdo->prepare("DELETE FROM prompts WHERE id = ?");
        $stmt->execute([$promptId]);
        redirect('../index.php');
    }
}

// Peut-on afficher le bouton supprimer ?
$canDelete = isLoggedIn() && ($_SESSION['user_id'] === (int)$prompt['user_id'] || isAdmin());
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container" style="max-width: 760px;">
    <a href="../index.php" style="color:#777; font-size:0.9rem;">← Retour à la bibliothèque</a>

    <div class="card" style="margin-top: 20px;">
        <!-- En-tête du prompt -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
            <div>
                <span class="badge"><?= clean($prompt['category_name']) ?></span>
                <h1 style="font-size: 1.4rem; margin-top: 12px; color: #e0e0e0;">
                    <?= clean($prompt['title']) ?>
                </h1>
                <p style="color:#666; font-size:0.85rem; margin-top:6px;">
                    👤 <?= clean($prompt['username']) ?>
                    &nbsp;·&nbsp;
                    🕐 <?= date('d/m/Y à H:i', strtotime($prompt['created_at'])) ?>
                </p>
            </div>

            <!-- Bouton supprimer (auteur ou admin uniquement) -->
            <?php if ($canDelete): ?>
            <form method="POST" onsubmit="return confirm('Supprimer ce prompt ?')">
                <button type="submit" name="delete" class="btn btn-danger btn-sm">🗑 Supprimer</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Contenu du prompt dans un bloc de code -->
        <div style="margin-top: 24px;">
            <label style="color:#aaa; font-size:0.8rem; display:block; margin-bottom:8px;">CONTENU DU PROMPT</label>
            <div style="background:#0f0f1a; border:1px solid #2a2a4a; border-radius:10px; padding:20px;">
                <pre style="white-space: pre-wrap; font-family: 'Courier New', monospace; font-size:0.9rem; color:#c9d1d9; line-height:1.7;"><?= clean($prompt['content']) ?></pre>
            </div>
        </div>

        <!-- Bouton copier -->
        <button onclick="copyPrompt()" class="btn btn-primary btn-sm" style="margin-top:16px;">
            📋 Copier le prompt
        </button>
        <span id="copy-msg" style="color:#5ce08a; font-size:0.8rem; margin-left:10px; display:none;">Copié !</span>
    </div>
</div>

<script>
// Copie le contenu du prompt dans le presse-papier
function copyPrompt() {
    const content = <?= json_encode($prompt['content']) ?>;
    navigator.clipboard.writeText(content).then(() => {
        const msg = document.getElementById('copy-msg');
        msg.style.display = 'inline';
        setTimeout(() => msg.style.display = 'none', 2000);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
