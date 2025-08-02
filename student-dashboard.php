<?php
session_start();
require_once 'config/db.php'; // À créer avec votre connexion PDO existante

// Vérification de connexion
if (!isset($_SESSION['user'])) {
    header('Location: index.php?msg=error_a');
    exit();
}

// Récupération des données étudiant
$stmt = $database->prepare("SELECT * FROM connection WHERE email = ?");
$stmt->execute([$_SESSION['user']['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant - Cosendai</title>
    <!-- Framework CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Personnalisé -->
    <link rel="stylesheet" href="style/stylestu.css">
</head>
<body class="dashboard-body">
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h3>Cosendai</h3>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#"><i class="fas fa-home"></i> Tableau de bord</a></li>
            <li><a href="#"><i class="fas fa-user-edit"></i> Modifier mon profil</a></li>
            <li><a href="#"><i class="fas fa-file-upload"></i> Documents</a></li>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> Calendrier</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <!-- Barre supérieure -->
        <header class="top-bar">
            <div class="user-profile">
                <img src="assets/images/avatars/<?= htmlspecialchars($user['id']) ?>.jpg" 
                     onerror="this.src='assets/images/avatars/default.jpg'" 
                     alt="Photo profil" class="avatar">
                <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
            </div>
        </header>

        <!-- Cartes principales -->
        <div class="dashboard-cards">
            <!-- Carte Statut -->
            <div class="card status-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Statut d'inscription</h5>
                    <div class="status-badge <?= htmlspecialchars($user['statut'] ?? 'en-attente') ?>">
                        <?= ucfirst(htmlspecialchars($user['statut'] ?? 'En attente')) ?>
                    </div>
                    <p class="card-text">Soumis le : <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
                </div>
            </div>

            <!-- Carte Profil -->
            <div class="card profile-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user-graduate"></i> Mon Profil</h5>
                    <ul class="profile-info">
                        <li><strong>Formation :</strong> <?= htmlspecialchars($user['formation'] ?? 'Non spécifiée') ?></li>
                        <li><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></li>
                        <li><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></li>
                    </ul>
                    <button class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Documents -->
        <div class="documents-section">
            <h4><i class="fas fa-file-alt"></i> Mes Documents</h4>
            <div class="documents-grid">
                <div class="doc-item">
                    <i class="fas fa-id-card doc-icon"></i>
                    <span>Pièce d'identité</span>
                    <span class="doc-status <?= $user['doc_id'] ? 'completed' : 'pending' ?>">
                        <?= $user['doc_id'] ? '✓ Reçu' : 'En attente' ?>
                    </span>
                </div>
                <!-- Ajoutez d'autres documents de la même manière -->
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <!-- Contenu du modal à ajouter -->
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/student-dashboard.js"></script>
</body>
</html>