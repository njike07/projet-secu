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
        $userId = (int)($_POST['user_id'] ?? 0);
        
        switch ($action) {
            case 'suspend_user':
                $stmt = $db->prepare("UPDATE utilisateurs SET statut = 'suspendu' WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Utilisateur suspendu avec succès';
                    $messageType = 'success';
                }
                break;
                
            case 'activate_user':
                $stmt = $db->prepare("UPDATE utilisateurs SET statut = 'actif' WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Utilisateur activé avec succès';
                    $messageType = 'success';
                }
                break;
                
            case 'promote_admin':
                $stmt = $db->prepare("UPDATE utilisateurs SET type = 'admin' WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Utilisateur promu administrateur';
                    $messageType = 'success';
                }
                break;
                
            case 'demote_user':
                $stmt = $db->prepare("UPDATE utilisateurs SET type = 'etudiant' WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Utilisateur rétrogradé en étudiant';
                    $messageType = 'success';
                }
                break;
        }
        
        // Journalisation
        if ($userId) {
            $stmt = $db->prepare("INSERT INTO modifications (user_id, action, details, ip_address, user_agent, modification_time) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'],
                'admin_action',
                "Action admin: {$action} sur utilisateur ID {$userId}",
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        }
    }
}

// Récupération de la liste des utilisateurs
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

$whereClause = "WHERE 1=1";
$params = [];

if ($search) {
    $whereClause .= " AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
    $searchParam = "%{$search}%";
    $params = [$searchParam, $searchParam, $searchParam];
}

if ($filter !== 'all') {
    $whereClause .= " AND type = ?";
    $params[] = $filter;
}

$stmt = $db->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM fiches_inscription f WHERE f.user_id = u.id) as has_fiche,
           (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id) as total_documents
    FROM utilisateurs u 
    {$whereClause}
    ORDER BY u.date_creation DESC
");
$stmt->execute($params);
$users = $stmt->fetchAll();

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
    <title>Gestion des Utilisateurs - Cosendai</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .search-bar { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .user-card { background: white; border-radius: 10px; padding: 20px; margin: 10px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-actif { background: #d4edda; color: #155724; }
        .status-inactif { background: #f8d7da; color: #721c24; }
        .status-suspendu { background: #fff3cd; color: #856404; }
        .type-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .type-admin { background: #dc3545; color: white; }
        .type-etudiant { background: #007bff; color: white; }
        .btn-action { padding: 5px 10px; margin: 2px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.8em; }
        .btn-suspend { background: #ffc107; color: #212529; }
        .btn-activate { background: #28a745; color: white; }
        .btn-promote { background: #dc3545; color: white; }
        .btn-demote { background: #6c757d; color: white; }
        .btn-view { background: #007bff; color: white; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .stats-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-item { background: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2em; font-weight: bold; color: #667eea; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h1>
                <p>Administration des comptes utilisateurs</p>
            </div>
            <div>
                <a href="admin_dashboard.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 5px; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i> Retour Dashboard
                </a>
                <a href="logout.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 5px;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>

    <div class="w3-container">
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques rapides -->
        <div class="stats-summary">
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['type'] === 'etudiant')); ?></div>
                <div>Étudiants</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['type'] === 'admin')); ?></div>
                <div>Administrateurs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['statut'] === 'actif')); ?></div>
                <div>Actifs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['statut'] === 'suspendu')); ?></div>
                <div>Suspendus</div>
            </div>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="search-bar">
            <form method="GET" style="display: flex; gap: 15px; align-items: center;">
                <div style="flex: 1;">
                    <input type="text" name="search" placeholder="Rechercher par nom, prénom ou email..." 
                           class="w3-input w3-border" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div>
                    <select name="filter" class="w3-select w3-border" style="width: 150px;">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="etudiant" <?php echo $filter === 'etudiant' ? 'selected' : ''; ?>>Étudiants</option>
                        <option value="admin" <?php echo $filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w3-button w3-blue">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des utilisateurs -->
        <div>
            <?php if (empty($users)): ?>
                <div class="w3-panel w3-pale-yellow w3-border">
                    <p>Aucun utilisateur trouvé avec ces critères.</p>
                </div>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                        </div>
                        
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0;">
                                <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                <span class="type-badge type-<?php echo $user['type']; ?>">
                                    <?php echo ucfirst($user['type']); ?>
                                </span>
                                <span class="status-badge status-<?php echo $user['statut']; ?>">
                                    <?php echo ucfirst($user['statut']); ?>
                                </span>
                            </h3>
                            <p style="margin: 5px 0; color: #666;">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                            <p style="margin: 5px 0; color: #666; font-size: 0.9em;">
                                <i class="fas fa-calendar"></i> Inscrit le <?php echo date('d/m/Y', strtotime($user['date_creation'])); ?>
                                <?php if ($user['derniere_connexion']): ?>
                                    | Dernière connexion: <?php echo date('d/m/Y H:i', strtotime($user['derniere_connexion'])); ?>
                                <?php endif; ?>
                            </p>
                            <p style="margin: 5px 0; color: #666; font-size: 0.9em;">
                                <i class="fas fa-file-alt"></i> 
                                <?php echo $user['has_fiche'] ? 'Fiche complétée' : 'Pas de fiche'; ?> |
                                <i class="fas fa-paperclip"></i> <?php echo $user['total_documents']; ?> document(s)
                            </p>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <?php if ($user['id'] !== $_SESSION['user_id']): // Ne pas permettre d'agir sur soi-même ?>
                                
                                <?php if ($user['statut'] === 'actif'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="suspend_user">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn-action btn-suspend" onclick="return confirm('Suspendre cet utilisateur ?')">
                                        <i class="fas fa-ban"></i> Suspendre
                                    </button>
                                </form>
                                <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="activate_user">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn-action btn-activate" onclick="return confirm('Activer cet utilisateur ?')">
                                        <i class="fas fa-check"></i> Activer
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($user['type'] === 'etudiant'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="promote_admin">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn-action btn-promote" onclick="return confirm('Promouvoir en administrateur ?')">
                                        <i class="fas fa-user-shield"></i> Promouvoir
                                    </button>
                                </form>
                                <?php elseif ($user['type'] === 'admin'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="demote_user">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn-action btn-demote" onclick="return confirm('Rétrograder en étudiant ?')">
                                        <i class="fas fa-user"></i> Rétrograder
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            
                            <?php if ($user['has_fiche']): ?>
                            <button class="btn-action btn-view" onclick="viewUserDetails(<?php echo $user['id']; ?>)">
                                <i class="fas fa-eye"></i> Voir Fiche
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function viewUserDetails(userId) {
            window.open('view_user_details.php?id=' + userId, '_blank', 'width=900,height=700');
        }
    </script>
</body>
</html>
