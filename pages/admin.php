<?php
/**
 * Page d'administration
 * Gestion des catégories + vue des contributeurs actifs
 * Réservée aux admins
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

// Protection : doit être admin
requireAdmin();

$pdo     = getConnection();
$error   = '';
$success = '';

// --- Action : Ajouter une catégorie ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = clean($_POST['category_name'] ?? '');

    if (empty($name)) {
        $error = "Le nom de la catégorie est obligatoire.";
    } elseif (strlen($name) > 50) {
        $error = "Le nom ne peut pas dépasser 50 caractères.";
    } else {
        // Vérifier que la catégorie n'existe pas déjà
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $error = "Cette catégorie existe déjà.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "Catégorie \"$name\" ajoutée avec succès !";
        }
    }
}

// --- Action : Supprimer une catégorie ---
if (isset($_GET['delete_category'])) {
    $catId = (int)$_GET['delete_category'];
    $stmt  = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$catId]);
    redirect('admin.php');
}

// --- Récupérer les données pour l'affichage ---

// Toutes les catégories avec le nombre de prompts associés
$categories = $pdo->query("
    SELECT categories.*, COUNT(prompts.id) AS prompt_count
    FROM categories
    LEFT JOIN prompts ON categories.id = prompts.category_id
    GROUP BY categories.id
    ORDER BY categories.name
")->fetchAll();

// Top contributeurs (classés par nombre de prompts)
$topContributors = $pdo->query("
    SELECT users.username, users.role, COUNT(prompts.id) AS prompt_count
    FROM users
    LEFT JOIN prompts ON users.id = prompts.user_id
    GROUP BY users.id
    ORDER BY prompt_count DESC
    LIMIT 10
")->fetchAll();

// Statistiques globales
$totalUsers   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPrompts = $pdo->query("SELECT COUNT(*) FROM prompts")->fetchColumn();
$totalCats    = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container">
    <h1 style="font-size:1.6rem; color:#a78bfa; margin-bottom:30px;">⚙️ Dashboard Admin</h1>

    <!-- Statistiques globales -->
    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin-bottom:30px;">
        <?php
        $stats = [
            ['👥', 'Utilisateurs',  $totalUsers],
            ['📝', 'Prompts',       $totalPrompts],
            ['🏷️', 'Catégories',   $totalCats],
        ];
        foreach ($stats as [$icon, $label, $value]):
        ?>
        <div class="card" style="text-align:center;">
            <div style="font-size:2rem;"><?= $icon ?></div>
            <div style="font-size:2rem; font-weight:bold; color:#a78bfa;"><?= $value ?></div>
            <div style="color:#777; font-size:0.85rem;"><?= $label ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

        <!-- === Gestion des catégories === -->
        <div class="card">
            <h2>🏷️ Catégories</h2>

            <?php if ($error):   ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <!-- Formulaire ajout catégorie -->
            <form method="POST" style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" name="category_name" placeholder="Nouvelle catégorie..."
                       style="flex:1; padding:8px 12px; background:#0f0f1a; border:1px solid #3a3a5a; border-radius:8px; color:#e0e0e0;">
                <button type="submit" name="add_category" class="btn btn-primary btn-sm">Ajouter</button>
            </form>

            <!-- Liste des catégories -->
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="color:#777; border-bottom:1px solid #2a2a4a;">
                        <th style="text-align:left; padding:8px 0;">Nom</th>
                        <th style="text-align:center;">Prompts</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr style="border-bottom:1px solid #1a1a3a;">
                        <td style="padding:10px 0;">
                            <span class="badge"><?= clean($cat['name']) ?></span>
                        </td>
                        <td style="text-align:center; color:#a78bfa;"><?= $cat['prompt_count'] ?></td>
                        <td style="text-align:right;">
                            <?php if ((int)$cat['prompt_count'] === 0): ?>
                                <a href="admin.php?delete_category=<?= $cat['id'] ?>"
                                   onclick="return confirm('Supprimer cette catégorie ?')"
                                   class="btn btn-danger btn-sm">🗑</a>
                            <?php else: ?>
                                <span style="color:#555; font-size:0.75rem;">en usage</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- === Top Contributeurs === -->
        <div class="card">
            <h2>🏆 Top Contributeurs</h2>
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="color:#777; border-bottom:1px solid #2a2a4a;">
                        <th style="text-align:left; padding:8px 0;">#</th>
                        <th style="text-align:left;">Utilisateur</th>
                        <th style="text-align:left;">Rôle</th>
                        <th style="text-align:right;">Prompts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topContributors as $index => $contributor): ?>
                    <tr style="border-bottom:1px solid #1a1a3a;">
                        <td style="padding:10px 0; color:#666;"><?= $index + 1 ?></td>
                        <td>
                            <?php
                            // Médaille pour les 3 premiers
                            $medals = ['🥇', '🥈', '🥉'];
                            echo ($medals[$index] ?? '') . ' ';
                            echo clean($contributor['username']);
                            ?>
                        </td>
                        <td>
                            <span style="font-size:0.75rem; color: <?= $contributor['role'] === 'admin' ? '#a78bfa' : '#5ce08a' ?>">
                                <?= $contributor['role'] ?>
                            </span>
                        </td>
                        <td style="text-align:right; color:#a78bfa; font-weight:bold;">
                            <?= $contributor['prompt_count'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
