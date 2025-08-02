<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$db = new Database();

// Vérification des droits d'accès admin
if (!$auth->checkPermission('admin')) {
    header("Location: pageLogin.php?msg=Accès refusé&type=error");
    exit();
}

$message = '';
$messageType = 'info';

// Traitement des actions admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de sécurité invalide';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'validate_inscription':
                $ficheId = (int)$_POST['fiche_id'];
                $stmt = $db->prepare("UPDATE fiches_inscription SET statut_inscription = 'validee', date_derniere_modification = NOW() WHERE id = ?");
                if ($stmt->execute([$ficheId])) {
                    $message = 'Inscription validée avec succès';
                    $messageType = 'success';
                }
                break;
                
            case 'reject_inscription':
                $ficheId = (int)$_POST['fiche_id'];
                $commentaire = Database::validateInput($_POST['commentaire'] ?? '');
                $stmt = $db->prepare("UPDATE fiches_inscription SET statut_inscription = 'refusee', commentaires_admin = ?, date_derniere_modification = NOW() WHERE id = ?");
                if ($stmt->execute([$commentaire, $ficheId])) {
                    $message = 'Inscription refusée';
                    $messageType = 'success';
                }
                break;
                
            case 'suspend_user':
                $userId = (int)$_POST['user_id'];
                $stmt = $db->prepare("UPDATE utilisateurs SET statut = 'suspendu' WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Utilisateur suspendu';
                    $messageType = 'success';
                }
                break;
        }
    }
}

// Récupération des statistiques
$stats = [];
$stmt = $db->prepare("SELECT COUNT(*) as total FROM utilisateurs WHERE type = 'etudiant'");
$stmt->execute();
$stats['total_etudiants'] = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM fiches_inscription WHERE statut_inscription = 'en_attente'");
$stmt->execute();
$stats['en_attente'] = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM fiches_inscription WHERE statut_inscription = 'validee'");
$stmt->execute();
$stats['validees'] = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM fiches_inscription WHERE statut_inscription = 'refusee'");
$stmt->execute();
$stats['refusees'] = $stmt->fetch()['total'];

// Récupération des inscriptions récentes
$stmt = $db->prepare("
    SELECT f.*, u.email, u.statut as user_statut 
    FROM fiches_inscription f 
    LEFT JOIN utilisateurs u ON f.user_id = u.id 
    ORDER BY f.date_soumission DESC 
    LIMIT 20
");
$stmt->execute();
$inscriptions = $stmt->fetchAll();

// Récupération des tentatives de connexion suspectes
$stmt = $db->prepare("
    SELECT email, ip_address, COUNT(*) as attempts, MAX(tentative_time) as last_attempt 
    FROM tentatives_connexion 
    WHERE success = 0 AND tentative_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    GROUP BY email, ip_address 
    HAVING attempts >= 3
    ORDER BY attempts DESC, last_attempt DESC
    LIMIT 10
");
$stmt->execute();
$suspicious_attempts = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $messageType = isset($_GET['type']) ? $_GET['type'] : 'info';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Cosendai</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; font-size: 0.9em; }
        .table-container { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-en-attente { background: #fff3cd; color: #856404; }
        .status-validee { background: #d4edda; color: #155724; }
        .status-refusee { background: #f8d7da; color: #721c24; }
        .btn-action { padding: 5px 10px; margin: 2px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.8em; }
        .btn-validate { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-view { background: #007bff; color: white; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .nav-menu { background: #343a40; padding: 10px 0; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; display: inline-block; }
        .nav-menu a:hover { background: #495057; }
        .suspicious-ip { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><i class="fas fa-shield-alt"></i> Dashboard Administrateur</h1>
                <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></p>
            </div>
            <div>
                <a href="logout.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 5px;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>

    <!-- Menu de navigation -->
    <div class="nav-menu">
        <a href="#stats"><i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="#inscriptions"><i class="fas fa-users"></i> Inscriptions</a>
        <a href="#security"><i class="fas fa-security"></i> Sécurité</a>
        <a href="admin_users.php"><i class="fas fa-user-cog"></i> Gestion Utilisateurs</a>
        <a href="admin_documents.php"><i class="fas fa-file-alt"></i> Documents</a>
    </div>

    <div class="w3-container">
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div id="stats">
            <h2><i class="fas fa-chart-bar"></i> Statistiques</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_etudiants']; ?></div>
                    <div class="stat-label">Total Étudiants</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['validees']; ?></div>
                    <div class="stat-label">Validées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['refusees']; ?></div>
                    <div class="stat-label">Refusées</div>
                </div>
            </div>
        </div>

        <!-- Inscriptions récentes -->
        <div id="inscriptions" class="table-container">
            <h2><i class="fas fa-users"></i> Inscriptions Récentes</h2>
            <div style="overflow-x: auto;">
                <table class="w3-table w3-striped">
                    <thead>
                        <tr class="w3-blue">
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Formation</th>
                            <th>Statut</th>
                            <th>Date Soumission</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inscriptions as $inscription): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($inscription['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($inscription['formation_demande'] ?? 'N/A'); ?></td>
                            <td>
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
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($inscription['date_soumission'])); ?></td>
                            <td>
                                <button class="btn-action btn-view" onclick="viewInscription(<?php echo $inscription['id']; ?>)">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                                <?php if ($inscription['statut_inscription'] === 'en_attente'): ?>
                                <form style="display: inline;" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="validate_inscription">
                                    <input type="hidden" name="fiche_id" value="<?php echo $inscription['id']; ?>">
                                    <button type="submit" class="btn-action btn-validate" onclick="return confirm('Valider cette inscription ?')">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                </form>
                                <button class="btn-action btn-reject" onclick="rejectInscription(<?php echo $inscription['id']; ?>)">
                                    <i class="fas fa-times"></i> Refuser
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sécurité -->
        <div id="security" class="table-container">
            <h2><i class="fas fa-security"></i> Tentatives de Connexion Suspectes</h2>
            <?php if (empty($suspicious_attempts)): ?>
                <p class="w3-text-green">Aucune tentative suspecte détectée dans les dernières 24h.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="w3-table w3-striped">
                        <thead>
                            <tr class="w3-red">
                                <th>Email</th>
                                <th>Adresse IP</th>
                                <th>Tentatives</th>
                                <th>Dernière Tentative</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suspicious_attempts as $attempt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attempt['email']); ?></td>
                                <td class="suspicious-ip"><?php echo htmlspecialchars($attempt['ip_address']); ?></td>
                                <td><strong><?php echo $attempt['attempts']; ?></strong></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($attempt['last_attempt'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour refuser une inscription -->
    <div id="rejectModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top">
            <div class="w3-container">
                <span onclick="document.getElementById('rejectModal').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                <h2>Refuser l'inscription</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="reject_inscription">
                    <input type="hidden" name="fiche_id" id="reject_fiche_id">
                    <p>
                        <label>Commentaire (optionnel):</label>
                        <textarea name="commentaire" class="w3-input w3-border" rows="4" placeholder="Raison du refus..."></textarea>
                    </p>
                    <p>
                        <button type="submit" class="w3-button w3-red">Confirmer le refus</button>
                        <button type="button" class="w3-button w3-grey" onclick="document.getElementById('rejectModal').style.display='none'">Annuler</button>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        function viewInscription(id) {
            window.open('view_inscription.php?id=' + id, '_blank', 'width=800,height=600');
        }

        function rejectInscription(id) {
            document.getElementById('reject_fiche_id').value = id;
            document.getElementById('rejectModal').style.display = 'block';
        }

        // Actualisation automatique des statistiques toutes les 30 secondes
        setInterval(function() {
            fetch('admin_stats_ajax.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = data.en_attente;
                })
                .catch(error => console.log('Erreur actualisation stats:', error));
        }, 30000);
    </script>
</body>
</html>
