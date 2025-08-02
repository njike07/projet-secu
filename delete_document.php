<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$db = new Database();

// Vérification de l'authentification
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Validation CSRF
if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide']);
    exit();
}

$documentId = (int)($_POST['id'] ?? 0);
if (!$documentId) {
    echo json_encode(['success' => false, 'message' => 'ID de document invalide']);
    exit();
}

try {
    // Récupérer les informations du document
    $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$documentId, $_SESSION['user_id']]);
    $document = $stmt->fetch();
    
    if (!$document) {
        echo json_encode(['success' => false, 'message' => 'Document non trouvé']);
        exit();
    }
    
    // Supprimer le fichier physique
    if (file_exists($document['chemin_fichier'])) {
        unlink($document['chemin_fichier']);
    }
    
    // Supprimer l'enregistrement de la base
    $stmt = $db->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$documentId, $_SESSION['user_id']]);
    
    // Journalisation
    $stmt = $db->prepare("INSERT INTO modifications (user_id, action, details, ip_address, user_agent, modification_time) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'],
        'delete_document',
        "Suppression du document: {$document['nom_fichier']}",
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Document supprimé avec succès']);
    
} catch (PDOException $e) {
    error_log("Document deletion error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
}
?>
