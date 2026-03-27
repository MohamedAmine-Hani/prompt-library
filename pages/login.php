<?php
/**
 * Page de connexion
 * Vérifie les identifiants et démarre la session
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

// Si déjà connecté, rediriger vers l'accueil
if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';

// --- Traitement du formulaire ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = clean($_POST['email']    ?? '');
    $password =       $_POST['password'] ?? '';

    // Validation simple
    if (empty($email) || empty($password)) {
        $error = "Email et mot de passe sont obligatoires.";
    } else {
        $pdo = getConnection();

        // Chercher l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Vérifier le mot de passe avec password_verify (compare au hash)
        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie : stocker les infos dans la session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            redirect('../index.php');
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container" style="max-width: 480px;">
    <div class="card">
        <h2>🔐 Connexion</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" name="email" value="<?= clean($_POST['email'] ?? '') ?>" placeholder="john@exemple.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Se connecter</button>
        </form>

        <p style="margin-top: 20px; text-align:center; color:#777; font-size:0.85rem;">
            Pas encore de compte ? <a href="register.php">S'inscrire</a>
        </p>

        <!-- Aide pour les tests -->
      
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
