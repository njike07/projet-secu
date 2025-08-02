<?php
require_once 'config.php';

function getStatistics($pdo) {
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM fiches_inscription");
    $stats['total'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as validees FROM fiches_inscription WHERE statut = 'validee'");
    $stats['validees'] = $stmt->fetch()['validees'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as en_attente FROM fiches_inscription WHERE statut = 'en_attente'");
    $stats['en_attente'] = $stmt->fetch()['en_attente'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as refusees FROM fiches_inscription WHERE statut = 'refusee'");
    $stats['refusees'] = $stmt->fetch()['refusees'];
    
    return $stats;
}

function getAllFiches($pdo) {
    $stmt = $pdo->query("
        SELECT f.*, u.nom as user_nom, u.prenom as user_prenom 
        FROM fiches_inscription f 
        JOIN utilisateurs u ON f.utilisateur_id = u.id 
        ORDER BY f.date_soumission DESC
    ");
    return $stmt->fetchAll();
}

function updateFicheStatus($pdo, $fiche_id, $statut, $commentaire = null) {
    $stmt = $pdo->prepare("UPDATE fiches_inscription SET statut = ?, commentaires_admin = ? WHERE id = ?");
    $stmt->execute([$statut, $commentaire, $fiche_id]);
    
    $stmt = $pdo->prepare("SELECT utilisateur_id FROM fiches_inscription WHERE id = ?");
    $stmt->execute([$fiche_id]);
    $user_id = $stmt->fetch()['utilisateur_id'];
    
    logModification($pdo, $fiche_id, $user_id, $statut === 'validee' ? 'validation' : 'refus', null, null, null, $commentaire);
}
?>