<?php
/**
 * Page d'inscription
 * Crée un nouveau compte utilisateur
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

// Si déjà connecté, rediriger vers l'accueil
if (isLoggedIn()) {
    redirect('../index.php');
}

$error   = ''; // Message d'erreur à afficher
$success = ''; // Message de succès

// --- Traitement du formulaire quand on clique sur "S'inscrire" ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Récupérer et nettoyer les données du formulaire
    $username = clean($_POST['username'] ?? '');
    $email    = clean($_POST['email']    ?? '');
    $password =       $_POST['password'] ?? ''; // Le mot de passe ne doit pas être nettoyé (caractères spéciaux OK)

    // 2. Validation : vérifier que les champs ne sont pas vides
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";

    // 3. Validation : format de l'email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";

    // 4. Validation : longueur du mot de passe
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";

    } else {
        $pdo = getConnection();

        // 5. Vérifier si le nom d'utilisateur ou l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $error = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            // 6. Hasher le mot de passe (jamais stocker en clair !)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 7. Insérer le nouvel utilisateur en base de données
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container" style="max-width: 480px;">
    <div class="card">
        <h2>✏️ Créer un compte</h2>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if ($error):   ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="login.php">Se connecter</a></div><?php endif; ?>

        <?php if (!$success): // Masquer le formulaire après succès ?>
        <form method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" value="<?= clean($_POST['username'] ?? '') ?>" placeholder="ex: john_dev" required>
            </div>
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" name="email" value="<?= clean($_POST['email'] ?? '') ?>" placeholder="john@exemple.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe <small style="color:#666">(6 caractères minimum)</small></label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Créer mon compte</button>
        </form>
        <?php endif; ?>

        <p style="margin-top: 20px; text-align:center; color:#777; font-size:0.85rem;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
