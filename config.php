<?php
// NJIKE Elsie
// Inclusion des fonctions de sécurité
require_once 'security.php';

// Configuration sécurisée des sessions
secureSession();

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'inscription');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonctions utilitaires
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validation sécurisée des mots de passe
function validatePassword($password) {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre";
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
    }
    return $errors;
}

// Protection contre les attaques par force brute
function checkBruteForce($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as attempts FROM tentatives_connexion WHERE email = ? AND succes = 0 AND date_tentative > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([$email]);
    $attempts = $stmt->fetchColumn();
    return $attempts >= 5; // Bloque après 5 tentatives en 15 minutes
}

// Protection CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function logModification($pdo, $fiche_id, $utilisateur_id, $action, $champ = null, $ancienne_valeur = null, $nouvelle_valeur = null, $commentaire = null) {
    $stmt = $pdo->prepare("INSERT INTO modifications (fiche_id, utilisateur_id, action, champ_modifie, ancienne_valeur, nouvelle_valeur, commentaire, ip_utilisateur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fiche_id, $utilisateur_id, $action, $champ, $ancienne_valeur, $nouvelle_valeur, $commentaire, $_SERVER['REMOTE_ADDR']]);
}

function logTentativeConnexion($pdo, $email, $succes) {
    $stmt = $pdo->prepare("INSERT INTO tentatives_connexion (email, ip_adresse, user_agent, succes, date_tentative) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown', $succes]);
}
?>