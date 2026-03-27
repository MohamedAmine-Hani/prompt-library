#  PromptLibrary 

Plateforme interne de Knowledge Management pour stocker, catégoriser et réutiliser vos prompts IA.

---

##  Structure des fichiers

```
prompt-library/
│
├── config/
│   ├── db.php          ← Connexion PDO à la base de données
│   └── database.sql    ← Script SQL : créer les tables + données de départ
│
├── includes/
│   ├── session.php     ← Fonctions utilitaires (isLoggedIn, redirect, clean...)
│   ├── header.php      ← En-tête HTML + navigation (inclus dans toutes les pages)
│   └── footer.php      ← Pied de page HTML (inclus dans toutes les pages)
│
├── pages/
│   ├── register.php    ← Inscription d'un nouveau compte
│   ├── login.php       ← Connexion
│   ├── logout.php      ← Déconnexion (détruit la session)
│   ├── add_prompt.php  ← Ajouter un prompt (connecté uniquement)
│   ├── view_prompt.php ← Voir un prompt en détail + copier + supprimer
│   └── admin.php       ← Dashboard admin (catégories + contributeurs)
│
└── index.php           ← Page d'accueil : liste + filtre des prompts
```

---

##  Installation (5 étapes)

### 1. Copier le projet
Placer le dossier `prompt-library/` dans votre dossier `htdocs` (XAMPP) ou `www` (WAMP).

### 2. Créer la base de données
- Ouvrir **phpMyAdmin** → http://localhost/phpmyadmin
- Cliquer sur **"Nouvelle base de données"** → saisir `prompt_library` → Créer
- Aller dans l'onglet **SQL** → coller le contenu de `config/database.sql` → Exécuter

### 3. Configurer la connexion
Ouvrir `config/db.php` et adapter si besoin :
```php
define('DB_USER', 'root');  // Votre utilisateur MySQL
define('DB_PASS', '');      // Votre mot de passe MySQL (vide par défaut sur XAMPP)
```

### 4. Lancer le projet
Ouvrir le navigateur : http://localhost/prompt-library/

### 5. Se connecter en admin
- Email : `admin@devgenius.com`
- Mot de passe : `admin123`

---

##  Structure de la base de données

| Table        | Colonnes principales                              |
|--------------|---------------------------------------------------|
| `users`      | id, username, email, password (hashé), role       |
| `categories` | id, name                                          |
| `prompts`    | id, title, content, user_id (FK), category_id (FK)|

---

##  Sécurité mise en place

| Mesure                    | Où ?                          |
|---------------------------|-------------------------------|
| `password_hash()`         | `register.php`                |
| `password_verify()`       | `login.php`                   |
| Prepared Statements PDO   | Toutes les requêtes SQL       |
| `htmlspecialchars()` (clean) | `session.php` → `clean()`  |
| Validation serveur        | Tous les formulaires          |
| Protection des pages      | `requireLogin()`, `requireAdmin()` |
| Vérification de propriété | Suppression dans `view_prompt.php` |
