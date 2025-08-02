<?php
// Configuration sécurisée des sessions
function secureSession() {
    // Configuration des cookies de session (seulement si session pas encore démarrée)
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // 0 pour localhost, 1 pour HTTPS
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
    
    // Régénération périodique de l'ID de session
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Protection contre les attaques XSS
function cleanOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Validation des entrées utilisateur
function validateInput($data, $type = 'string') {
    $data = trim($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'string':
            return preg_match('/^[a-zA-ZÀ-ÿ\s\-\']{2,50}$/', $data) ? $data : false;
        case 'phone':
            return preg_match('/^[\+]?[0-9\s\-\(\)]{8,20}$/', $data) ? $data : false;
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

// Protection contre les injections SQL (vérification supplémentaire)
function sanitizeForDB($data) {
    // Suppression des caractères dangereux
    $dangerous = ['<script', '</script>', 'javascript:', 'vbscript:', 'onload=', 'onerror='];
    $data = str_ireplace($dangerous, '', $data);
    return trim($data);
}

// Vérification des permissions
function checkPermission($requiredRole) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $requiredRole) {
        header('HTTP/1.1 403 Forbidden');
        header('Location: pageLogin.php?msg=Accès non autorisé');
        exit();
    }
}

// Limitation du taux de requêtes
function rateLimiting($identifier, $maxRequests = 10, $timeWindow = 60) {
    $key = 'rate_limit_' . $identifier;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'start_time' => time()];
        return true;
    }
    
    $data = $_SESSION[$key];
    
    if (time() - $data['start_time'] > $timeWindow) {
        $_SESSION[$key] = ['count' => 1, 'start_time' => time()];
        return true;
    }
    
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}
?>