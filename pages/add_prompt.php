<?php
/**
 * Page pour ajouter un nouveau prompt
 * Réservée aux utilisateurs connectés
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

// Protection : doit être connecté
requireLogin();

$pdo     = getConnection();
$error   = '';
$success = '';

// Récupérer les catégories pour le menu déroulant
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// --- Traitement du formulaire ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title      = clean($_POST['title']       ?? '');
    $content    = clean($_POST['content']     ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    // Validation
    if (empty($title) || empty($content) || $categoryId === 0) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (strlen($title) > 150) {
        $error = "Le titre ne peut pas dépasser 150 caractères.";
    } else {
        // Insérer le prompt (user_id vient de la session)
        $stmt = $pdo->prepare("
            INSERT INTO prompts (title, content, user_id, category_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$title, $content, $_SESSION['user_id'], $categoryId]);

        $success = "Prompt ajouté avec succès ! 🎉";
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container" style="max-width: 640px;">
    <div class="card">
        <h2>➕ Ajouter un prompt</h2>

        <?php if ($error):   ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="../index.php">Voir la bibliothèque</a></div><?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Titre du prompt</label>
                <input type="text" name="title"
                       value="<?= clean($_POST['title'] ?? '') ?>"
                       placeholder="ex: Générer une fonction PHP sécurisée" required>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="category_id" required>
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= clean($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Contenu du prompt</label>
                <textarea name="content" placeholder="Collez ici votre prompt..." required
                          style="height: 200px;"><?= clean($_POST['content'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">💾 Enregistrer le prompt</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
