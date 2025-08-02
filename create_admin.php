<?php
/**
 * Script de création du compte administrateur par défaut
 * À exécuter une seule fois après l'installation
 */

require_once 'config/database.php';
require_once 'config/auth.php';

try {
    $db = new Database();
    $auth = new Auth();
    
    // Vérifier si un admin existe déjà
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE type = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetch()['count'];
    
    if ($adminCount > 0) {
        echo "Un administrateur existe déjà dans le système.\n";
        exit();
    }
    
    // Créer le compte administrateur
    $adminData = [
        'nom' => 'Administrateur',
        'prenom' => 'Système',
        'email' => 'admin@cosendai.com',
        'password' => 'AdminPass123!',
        'type' => 'admin'
    ];
    
    $result = $auth->register(
        $adminData['nom'],
        $adminData['prenom'], 
        $adminData['email'],
        $adminData['password'],
        $adminData['type']
    );
    
    if ($result['success']) {
        echo "✅ Compte administrateur créé avec succès!\n";
        echo "📧 Email: " . $adminData['email'] . "\n";
        echo "🔑 Mot de passe: " . $adminData['password'] . "\n";
        echo "⚠️  IMPORTANT: Changez ce mot de passe après la première connexion!\n";
    } else {
        echo "❌ Erreur lors de la création: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
