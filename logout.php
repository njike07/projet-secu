<?php
session_start();
require_once 'config/auth.php';

$auth = new Auth();

// Déconnexion sécurisée
$auth->logout();

// Redirection avec message
header("Location: pageLogin.php?msg=Vous avez été déconnecté avec succès&type=success");
exit();
?>
