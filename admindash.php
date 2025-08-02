<?php
require_once 'config.php';
require_once 'admin_functions.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur'){
    header("Location: pageLogin.php");
    exit();
}

$stats = getStatistics($pdo);
$fiches = getAllFiches($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosendai - Tableau de bord Administrateur</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/admindash.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: var(--dark-color);
            overflow-x: hidden;
            padding-top: 20px;
            transition: 0.3s;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: var(--light-color);
            display: block;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid var(--primary-color);
        }
        
        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .logo h2 {
            color: white;
            margin: 0;
            font-size: 22px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: 0.3s;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
        }
        
        .header h1 {
            margin: 0;
            color: var(--dark-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .card-value {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .card-footer {
            font-size: 14px;
            color: #777;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 13px;
        }
        
        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .search-box {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        
        .filter-container {
            display: flex;
            gap: 10px;
        }
        
        .filter-select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-color);
        }
        
        .section {
            display: none;
        }
        
        .section.active {
            display: block;
        }
        
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
            }
            
            .search-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <h2><i class="fas fa-graduation-cap"></i> Cosendai</h2>
        </div>
        <a href="#" class="active" onclick="showSection('dashboard')"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
        <a href="#" onclick="showSection('students')"><i class="fas fa-users"></i> Étudiants</a>
        <a href="#" onclick="showSection('fiches')"><i class="fas fa-file-alt"></i> Fiches d'inscription</a>
        <a href="#" onclick="showSection('documents')"><i class="fas fa-file-upload"></i> Documents</a>
        <a href="#" onclick="showSection('statistics')"><i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Tableau de bord Administrateur</h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Administrateur</span>
            </div>
        </div>

        <!-- Dashboard Section -->
        <div id="dashboard-section" class="section active">
        <!-- Cards -->
        <div class="cards-container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inscriptions totales</h3>
                    <div class="card-icon" style="background-color: var(--primary-color);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="card-value" id="totalRegistrations"><?php echo $stats['total'] ?></div>
                <div class="card-footer">Total des inscriptions</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inscriptions validées</h3>
                    <div class="card-icon" style="background-color: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="card-value" id="approvedRegistrations"><?php echo $stats['validees'] ?></div>
                <div class="card-footer">Inscriptions validées</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">En attente</h3>
                    <div class="card-icon" style="background-color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card-value" id="pendingRegistrations"><?php echo $stats['en_attente'] ?></div>
                <div class="card-footer">En attente de validation</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rejetées</h3>
                    <div class="card-icon" style="background-color: var(--accent-color);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="card-value" id="rejectedRegistrations"><?php echo $stats['refusees'] ?></div>
                <div class="card-footer">Inscriptions refusées</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <h3 class="chart-title">Inscriptions par mois</h3>
            <canvas id="registrationsChart" height="100"></canvas>
        </div>

        <!-- Recent Registrations Table -->
        <div class="table-container">
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Rechercher un étudiant..." id="searchInput">
                <div class="filter-container">
                    <select class="filter-select" id="statusFilter">
                        <option value="all">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Validé</option>
                        <option value="rejected">Rejeté</option>
                    </select>
                    <select class="filter-select" id="dateFilter">
                        <option value="all">Toutes dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                    </select>
                </div>
            </div>
            
            <table id="registrationsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Formation</th>
                        <th>Date inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fiches as $fiche): ?>
                    <tr>
                        <td>#<?php echo $fiche['id'] ?></td>
                        <td><?php echo $fiche['nom'] . ' ' . $fiche['prenom'] ?></td>
                        <td><?php echo $fiche['formation_demandee'] ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fiche['date_soumission'])) ?></td>
                        <td>
                            <span class="status status-<?php echo $fiche['statut'] === 'validee' ? 'approved' : ($fiche['statut'] === 'refusee' ? 'rejected' : 'pending') ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $fiche['statut'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="admin_view_fiche.php?id=<?php echo $fiche['id'] ?>" class="btn btn-primary btn-sm">Voir</a>
                            <a href="admin_edit_fiche.php?id=<?php echo $fiche['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>

        <!-- Students Section -->
        <div id="students-section" class="section">
            <h2>Gestion des Étudiants</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date inscription</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM utilisateurs WHERE role = 'etudiant' ORDER BY date_creation DESC");
                        while($etudiant = $stmt->fetch()): 
                        ?>
                        <tr>
                            <td>#<?php echo $etudiant['id'] ?></td>
                            <td><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></td>
                            <td><?php echo htmlspecialchars($etudiant['email']) ?></td>
                            <td><?php echo date('d/m/Y', strtotime($etudiant['date_creation'])) ?></td>
                            <td><span class="status <?php echo $etudiant['actif'] ? 'status-approved' : 'status-rejected' ?>"><?php echo $etudiant['actif'] ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Voir</button>
                                <button class="btn btn-danger btn-sm"><?php echo $etudiant['actif'] ? 'Désactiver' : 'Activer' ?></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fiches Section -->
        <div id="fiches-section" class="section">
            <h2>Fiches d'inscription</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Formation</th>
                            <th>Date soumission</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fiches as $fiche): ?>
                        <tr>
                            <td>#<?php echo $fiche['id'] ?></td>
                            <td><?php echo $fiche['nom'] . ' ' . $fiche['prenom'] ?></td>
                            <td><?php echo $fiche['formation_demandee'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($fiche['date_soumission'])) ?></td>
                            <td>
                                <span class="status status-<?php echo $fiche['statut'] === 'validee' ? 'approved' : ($fiche['statut'] === 'refusee' ? 'rejected' : 'pending') ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $fiche['statut'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_view_fiche.php?id=<?php echo $fiche['id'] ?>" class="btn btn-primary btn-sm">Voir</a>
                                <a href="admin_edit_fiche.php?id=<?php echo $fiche['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Documents Section -->
        <div id="documents-section" class="section">
            <h2>Gestion des Documents</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Fiche</th>
                            <th>Étudiant</th>
                            <th>Type Document</th>
                            <th>Nom Fichier</th>
                            <th>Date upload</th>
                            <th>Taille</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stmt = $pdo->query("SELECT d.*, f.nom, f.prenom FROM documents d JOIN fiches_inscription f ON d.fiche_id = f.id ORDER BY d.date_upload DESC");
                        $hasDocuments = false;
                        while($doc = $stmt->fetch()): 
                            $hasDocuments = true;
                        ?>
                        <tr>
                            <td>#<?php echo $doc['fiche_id'] ?></td>
                            <td><?php echo htmlspecialchars($doc['nom'] . ' ' . $doc['prenom']) ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $doc['type_document'])) ?></td>
                            <td><?php echo htmlspecialchars($doc['nom_fichier']) ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($doc['date_upload'])) ?></td>
                            <td><?php echo round($doc['taille_fichier']/1024, 1) ?> KB</td>
                            <td>
                                <a href="<?php echo $doc['chemin_fichier'] ?>" class="btn btn-primary btn-sm" target="_blank">Voir</a>
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </td>
                        </tr>
                        <?php endwhile; 
                        if (!$hasDocuments): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i><br>
                                Aucun document n'a encore été importé
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics Section -->
        <div id="statistics-section" class="section">
            <h2>Statistiques détaillées</h2>
            
            <div class="cards-container">
                <?php
                $totalUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'etudiant'")->fetchColumn();
                $totalDocs = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
                $avgCompletion = $pdo->query("SELECT AVG(CASE WHEN statut = 'validee' THEN 100 WHEN statut = 'en_attente' THEN 50 ELSE 0 END) FROM fiches_inscription")->fetchColumn();
                ?>
                <div class="card">
                    <h3>Total Étudiants</h3>
                    <div class="card-value"><?php echo $totalUsers ?></div>
                </div>
                <div class="card">
                    <h3>Documents Uploadés</h3>
                    <div class="card-value"><?php echo $totalDocs ?></div>
                </div>
                <div class="card">
                    <h3>Taux de Complétion</h3>
                    <div class="card-value"><?php echo round($avgCompletion, 1) ?>%</div>
                </div>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Inscriptions par mois (Année courante)</h3>
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
            
            <div class="chart-container" style="max-width: 400px; margin: 0 auto;">
                <h3 class="chart-title">Répartition par formation</h3>
                <canvas id="formationChart" width="300" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get dynamic data from PHP
        <?php
        $monthlyData = [];
        for($i = 1; $i <= 12; $i++) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM fiches_inscription WHERE MONTH(date_soumission) = ? AND YEAR(date_soumission) = YEAR(CURDATE())");
            $stmt->execute([$i]);
            $monthlyData[] = $stmt->fetchColumn();
        }
        
        $formationStats = $pdo->query("SELECT formation_demandee, COUNT(*) as count FROM fiches_inscription WHERE formation_demandee IS NOT NULL AND formation_demandee != '' GROUP BY formation_demandee")->fetchAll();
        $formations = [];
        $counts = [];
        foreach($formationStats as $stat) {
            $formations[] = $stat['formation_demandee'];
            $counts[] = $stat['count'];
        }
        ?>
        var monthlyData = <?php echo json_encode($monthlyData); ?>;
        var formationData = {
            labels: <?php echo json_encode($formations); ?>,
            counts: <?php echo json_encode($counts); ?>
        };
    </script>
    <script src="js/admin-dashboard.js"></script>
</body>
</html>