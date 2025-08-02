<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosendai - Tableau de bord Administrateur</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
        <a href="#"><i class="fas fa-users"></i> Étudiants</a>
        <a href="#"><i class="fas fa-file-alt"></i> Fiches d'inscription</a>
        <a href="#"><i class="fas fa-file-upload"></i> Documents</a>
        <a href="#"><i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
        <a href="#"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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

        <!-- Cards -->
        <div class="cards-container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inscriptions totales</h3>
                    <div class="card-icon" style="background-color: var(--primary-color);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="card-value" id="totalRegistrations">245</div>
                <div class="card-footer">+12 cette semaine</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inscriptions validées</h3>
                    <div class="card-icon" style="background-color: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="card-value" id="approvedRegistrations">189</div>
                <div class="card-footer">77% du total</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">En attente</h3>
                    <div class="card-icon" style="background-color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card-value" id="pendingRegistrations">42</div>
                <div class="card-footer">14 à traiter aujourd'hui</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rejetées</h3>
                    <div class="card-icon" style="background-color: var(--accent-color);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="card-value" id="rejectedRegistrations">14</div>
                <div class="card-footer">5.7% du total</div>
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
                    <tr>
                        <td>#1001</td>
                        <td>Jean Dupont</td>
                        <td>Informatique</td>
                        <td>15/06/2023</td>
                        <td><span class="status status-approved">Validé</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Voir</button>
                            <button class="btn btn-primary btn-sm">Modifier</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1002</td>
                        <td>Marie Lambert</td>
                        <td>Gestion</td>
                        <td>16/06/2023</td>
                        <td><span class="status status-pending">En attente</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Voir</button>
                            <button class="btn btn-primary btn-sm">Modifier</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1003</td>
                        <td>Pierre Martin</td>
                        <td>Droit</td>
                        <td>17/06/2023</td>
                        <td><span class="status status-rejected">Rejeté</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Voir</button>
                            <button class="btn btn-primary btn-sm">Modifier</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1004</td>
                        <td>Sophie Bernard</td>
                        <td>Informatique</td>
                        <td>18/06/2023</td>
                        <td><span class="status status-approved">Validé</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Voir</button>
                            <button class="btn btn-primary btn-sm">Modifier</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1005</td>
                        <td>Luc Petit</td>
                        <td>Gestion</td>
                        <td>19/06/2023</td>
                        <td><span class="status status-pending">En attente</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Voir</button>
                            <button class="btn btn-primary btn-sm">Modifier</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js Implementation
        document.addEventListener('DOMContentLoaded', function() {
            // Registration Statistics Chart
            const ctx = document.getElementById('registrationsChart').getContext('2d');
            const registrationsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Inscriptions',
                        data: [15, 22, 18, 25, 30, 42, 35, 28, 40, 38, 45, 50],
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('#registrationsTable tbody tr');
                
                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const formation = row.cells[2].textContent.toLowerCase();
                    if (name.includes(filter) || formation.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Filter by status
            const statusFilter = document.getElementById('statusFilter');
            statusFilter.addEventListener('change', function() {
                const filterValue = statusFilter.value;
                const rows = document.querySelectorAll('#registrationsTable tbody tr');
                
                rows.forEach(row => {
                    const status = row.cells[4].textContent.toLowerCase();
                    if (filterValue === 'all' || status.includes(filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Simulate data loading
            setTimeout(() => {
                document.getElementById('totalRegistrations').textContent = '256';
                document.getElementById('approvedRegistrations').textContent = '195';
                document.getElementById('pendingRegistrations').textContent = '47';
                document.getElementById('rejectedRegistrations').textContent = '14';
            }, 1500);
        });

        // Toggle sidebar on small screens
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (sidebar.style.width === '250px') {
                sidebar.style.width = '0';
                mainContent.style.marginLeft = '0';
            } else {
                sidebar.style.width = '250px';
                mainContent.style.marginLeft = '250px';
            }
        }
    </script>
</body>
</html>