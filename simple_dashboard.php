<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: simple_login.php");
    exit();
}

// Configuration base de données
$host = 'localhost';
$dbname = 'inscription-2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: simple_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cosendai</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px;
        }
        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        .menu-icon {
            font-size: 3em;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .menu-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .menu-description {
            color: #666;
            font-size: 0.9em;
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
        .status-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 10px;
            padding: 15px;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><i class="fas fa-graduation-cap"></i> Cosendai</h1>
                <p style="margin: 0; opacity: 0.9;">Portail Étudiant - Dashboard</p>
            </div>
            <a href="simple_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>

    <div class="welcome-card">
        <div class="user-avatar">
            <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
        </div>
        <h2>Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> !</h2>
        <p style="color: #666;">Votre espace personnel pour gérer votre inscription</p>
    </div>

    <div class="status-info">
        <i class="fas fa-info-circle" style="color: #2196f3;"></i>
        <strong>Version Simplifiée Active</strong><br>
        Cette version fonctionne avec votre base de données existante. 
        Pour accéder aux fonctionnalités complètes, mettez à jour votre base de données.
    </div>

    <div class="menu-grid">
        <a href="registrationform.php" class="menu-card">
            <div class="menu-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="menu-title">Mon Profil</div>
            <div class="menu-description">Modifier mes informations personnelles</div>
        </a>

        <a href="documents.php" class="menu-card">
            <div class="menu-icon">
                <i class="fas fa-file-upload"></i>
            </div>
            <div class="menu-title">Mes Documents</div>
            <div class="menu-description">Télécharger et gérer mes documents</div>
        </a>

        <div class="menu-card" style="opacity: 0.6; cursor: not-allowed;">
            <div class="menu-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="menu-title">Suivi Inscription</div>
            <div class="menu-description">Statut de ma demande d'inscription</div>
        </div>

        <div class="menu-card" style="opacity: 0.6; cursor: not-allowed;">
            <div class="menu-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="menu-title">Notifications</div>
            <div class="menu-description">Messages et alertes importantes</div>
        </div>
    </div>

    <div style="text-align: center; padding: 40px; color: #666;">
        <p><i class="fas fa-database"></i> Base de données: <?php echo htmlspecialchars($dbname); ?></p>
        <p><i class="fas fa-user"></i> Connecté en tant que: <?php echo htmlspecialchars($user['email']); ?></p>
    </div>
</body>
</html>
