<?php
session_start();
require_once 'config/oauth.php';

$oauth = new OAuth();
$message = '';
$messageType = 'error';

try {
    $provider = $_GET['provider'] ?? '';
    $code = $_GET['code'] ?? '';
    $state = $_GET['state'] ?? '';
    $error = $_GET['error'] ?? '';
    
    // Vérifier s'il y a une erreur
    if ($error) {
        throw new Exception('Erreur OAuth: ' . $error);
    }
    
    if (!$code || !$state) {
        throw new Exception('Paramètres OAuth manquants');
    }
    
    $user = null;
    
    switch ($provider) {
        case 'google':
            $user = $oauth->handleGoogleCallback($code, $state);
            break;
        case 'facebook':
            $user = $oauth->handleFacebookCallback($code, $state);
            break;
        default:
            throw new Exception('Provider OAuth non supporté');
    }
    
    if ($user) {
        // Démarrer la session
        $oauth->startOAuthSession($user);
        
        // Redirection selon le type d'utilisateur
        if ($user['type'] === 'admin') {
            header("Location: admin_dashboard.php?msg=Connexion réussie via " . ucfirst($provider) . "&type=success");
        } else {
            header("Location: index.php?msg=Connexion réussie via " . ucfirst($provider) . "&type=success");
        }
        exit();
    }
    
} catch (Exception $e) {
    error_log("OAuth error: " . $e->getMessage());
    $message = $e->getMessage();
    $messageType = 'error';
}

// En cas d'erreur, rediriger vers la page de connexion
header("Location: pageLogin.php?msg=" . urlencode($message) . "&type=" . $messageType);
exit();
?>
