<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$db = new Database();

// Vérification des droits d'accès
if (!$auth->checkPermission('admin')) {
    header("Location: pageLogin.php?msg=Accès refusé&type=error");
    exit();
}

$inscriptionId = (int)($_GET['id'] ?? 0);
if (!$inscriptionId) {
    die('ID d\'inscription invalide');
}

// Récupération des données de l'inscription
$stmt = $db->prepare("
    SELECT f.*, u.email, u.date_creation as user_creation, u.statut as user_statut
    FROM fiches_inscription f 
    LEFT JOIN utilisateurs u ON f.user_id = u.id 
    WHERE f.id = ?
");
$stmt->execute([$inscriptionId]);
$inscription = $stmt->fetch();

if (!$inscription) {
    die('Inscription non trouvée');
}

// Récupération des documents associés
$stmt = $db->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY date_upload DESC");
$stmt->execute([$inscription['user_id']]);
$documents = $stmt->fetchAll();

// Récupération de l'historique des modifications
$stmt = $db->prepare("
    SELECT m.*, u.nom, u.prenom 
    FROM modifications m 
    LEFT JOIN utilisateurs u ON m.user_id = u.id 
    WHERE m.details LIKE ? OR m.user_id = ?
    ORDER BY m.modification_time DESC 
    LIMIT 10
");
$stmt->execute(["%inscription%{$inscriptionId}%", $inscription['user_id']]);
$historique = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Inscription - <?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; }
        .section { background: white; margin: 15px; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .field-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 15px 0; }
        .field { margin-bottom: 10px; }
        .field-label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        .field-value { padding: 8px; background: #f8f9fa; border-radius: 4px; border: 1px solid #dee2e6; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.9em; font-weight: bold; display: inline-block; }
        .status-en-attente { background: #fff3cd; color: #856404; }
        .status-validee { background: #d4edda; color: #155724; }
        .status-refusee { background: #f8d7da; color: #721c24; }
        .document-item { background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .history-item { background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px; border-left: 4px solid #28a745; }
        .btn { padding: 8px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-user-graduate"></i> Détails de l'inscription</h1>
        <p><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></p>
    </div>

    <!-- Informations personnelles -->
    <div class="section">
        <h2><i class="fas fa-user"></i> Informations Personnelles</h2>
        <div class="field-group">
            <div class="field">
                <span class="field-label">Nom:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['nom']); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Prénom:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['prenom']); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Email:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['email'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Date de naissance:</span>
                <div class="field-value"><?php echo $inscription['date_de_naissance'] ? date('d/m/Y', strtotime($inscription['date_de_naissance'])) : 'N/A'; ?></div>
            </div>
            <div class="field">
                <span class="field-label">Lieu de naissance:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['lieu_naissance'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Sexe:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['sexe'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Nationalité:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['nationalite'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Téléphone:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['telephone'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Adresse postale:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['adresse_postale'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>

    <!-- Informations académiques -->
    <div class="section">
        <h2><i class="fas fa-graduation-cap"></i> Informations Académiques</h2>
        <div class="field-group">
            <div class="field">
                <span class="field-label">Dernier diplôme obtenu:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['dernier_diplome_obtenue'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Établissement précédent:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['etablisement_precedent'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Formation demandée:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['formation_demande'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Spécialisation:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['specialisation'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>

    <!-- Contact d'urgence -->
    <div class="section">
        <h2><i class="fas fa-phone"></i> Contact d'Urgence</h2>
        <div class="field-group">
            <div class="field">
                <span class="field-label">Nom du contact:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['nom_contact_urgence'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Relation:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['relation_etudiants'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Téléphone:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['telephone_contact'] ?? 'N/A'); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Email:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['email_contact'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>

    <!-- Statut et suivi -->
    <div class="section">
        <h2><i class="fas fa-clipboard-check"></i> Statut et Suivi</h2>
        <div class="field-group">
            <div class="field">
                <span class="field-label">Statut de l'inscription:</span>
                <div class="field-value">
                    <span class="status-badge status-<?php echo $inscription['statut_inscription']; ?>">
                        <?php 
                        switch($inscription['statut_inscription']) {
                            case 'en_attente': echo 'En Attente'; break;
                            case 'validee': echo 'Validée'; break;
                            case 'refusee': echo 'Refusée'; break;
                            default: echo 'Inconnu';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="field">
                <span class="field-label">Date de soumission:</span>
                <div class="field-value"><?php echo date('d/m/Y H:i:s', strtotime($inscription['date_soumission'])); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Dernière modification:</span>
                <div class="field-value"><?php echo date('d/m/Y H:i:s', strtotime($inscription['date_derniere_modification'])); ?></div>
            </div>
            <div class="field">
                <span class="field-label">Commentaires admin:</span>
                <div class="field-value"><?php echo htmlspecialchars($inscription['commentaires_admin'] ?? 'Aucun commentaire'); ?></div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="section">
        <h2><i class="fas fa-file-alt"></i> Documents</h2>
        <?php if (empty($documents)): ?>
            <p class="w3-text-grey">Aucun document uploadé</p>
        <?php else: ?>
            <?php foreach ($documents as $doc): ?>
            <div class="document-item">
                <strong><?php echo htmlspecialchars($doc['type_document']); ?></strong> - 
                <?php echo htmlspecialchars($doc['nom_fichier']); ?>
                <span class="w3-right">
                    <span class="status-badge status-<?php echo $doc['statut']; ?>"><?php echo ucfirst($doc['statut']); ?></span>
                    <a href="download_document.php?id=<?php echo $doc['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                </span>
                <br><small>Uploadé le <?php echo date('d/m/Y H:i', strtotime($doc['date_upload'])); ?></small>
                <?php if ($doc['commentaire_admin']): ?>
                    <br><em>Commentaire: <?php echo htmlspecialchars($doc['commentaire_admin']); ?></em>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Historique -->
    <div class="section">
        <h2><i class="fas fa-history"></i> Historique des Modifications</h2>
        <?php if (empty($historique)): ?>
            <p class="w3-text-grey">Aucune modification enregistrée</p>
        <?php else: ?>
            <?php foreach ($historique as $entry): ?>
            <div class="history-item">
                <strong><?php echo htmlspecialchars($entry['action']); ?></strong>
                <?php if ($entry['nom']): ?>
                    par <?php echo htmlspecialchars($entry['prenom'] . ' ' . $entry['nom']); ?>
                <?php endif; ?>
                <span class="w3-right"><?php echo date('d/m/Y H:i:s', strtotime($entry['modification_time'])); ?></span>
                <br><small><?php echo htmlspecialchars($entry['details']); ?></small>
                <br><small class="w3-text-grey">IP: <?php echo htmlspecialchars($entry['ip_address']); ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="section">
        <h2><i class="fas fa-cogs"></i> Actions</h2>
        <a href="edit_inscription.php?id=<?php echo $inscriptionId; ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <?php if ($inscription['statut_inscription'] === 'en_attente'): ?>
        <a href="admin_dashboard.php?action=validate&id=<?php echo $inscriptionId; ?>" class="btn btn-success" onclick="return confirm('Valider cette inscription ?')">
            <i class="fas fa-check"></i> Valider
        </a>
        <a href="admin_dashboard.php?action=reject&id=<?php echo $inscriptionId; ?>" class="btn btn-danger" onclick="return confirm('Refuser cette inscription ?')">
            <i class="fas fa-times"></i> Refuser
        </a>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Fermer
        </button>
    </div>

    <script>
        // Impression automatique si demandée
        if (window.location.search.includes('print=1')) {
            window.print();
        }
    </script>
</body>
</html>
