<?php
require_once 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant'){
    header("Location: pageLogin.php?msg=Veuillez vous connecter !");
    exit();
}

$user = $_SESSION['user'];

// Récupérer la fiche d'inscription si elle existe
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$fiche = $stmt->fetch();

// Calculer le pourcentage de complétion
$completion = 0;
if ($fiche) {
    $fields = ['nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe', 'nationalite', 'adresse_postale', 'email', 'telephone', 'dernier_diplome', 'etablissement_precedent', 'formation_demandee', 'specialisation', 'nom_contact_urgence', 'relation_contact', 'telephone_contact', 'email_contact'];
    $filled = 0;
    foreach ($fields as $field) {
        if (!empty($fiche[$field])) $filled++;
    }
    $completion = round(($filled / count($fields)) * 100);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Cosendai</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/index.css">

</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> Cosendai
        </div>
        <div class="user-menu">
            <div class="user-avatar">
              <?php echo substr($user['nom'],0,1) . substr($user['prenom'],0,1) ?>

            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <!-- Carte de bienvenue -->
        <div class="welcome-card">
            <h1>Bienvenue, <?php echo $user['prenom'] ?></h1>
            <p>Votre portail étudiant pour gérer votre inscription</p>
            <div class="status-badge">Statut : <?php echo $fiche ? ucfirst(str_replace('_', ' ', $fiche['statut'])) : 'Fiche non créée' ?></div>
        </div>

        <!-- Progression -->
        <div class="progress-container">
            <h3 class="progress-title">Progression de votre inscription</h3>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="progress-text"><?php echo $completion ?>% complété</div>
        </div>

        <!-- Cartes d'action -->
        <div class="action-cards">
            <div class="action-card" onclick="window.location.href='registrationform.php'" style="cursor: pointer;">
                <i class="fas fa-edit"></i>
                <h3>Commencer mon inscription</h3>
                <p>Finalisez votre dossier en remplissant tous les champs obligatoires</p>
            </div>
            
            
            <div class="action-card" onclick="location.href='documents.php'">
                <i class="fas fa-file-upload"></i>
                <h3>Documents à fournir</h3>
                <p>Consultez la liste des documents requis pour valider votre inscription</p>
            </div>
            
            <div class="action-card" onclick="location.href='studash.php'">
                <i class="fas fa-user"></i>
                <h3>Mon Espace</h3>
                <p>Visualisez et modifiez vos informations personnelles</p>
            </div>
            
        </div>

        <!-- Notification -->
        <div class="notification">
            <h3><i class="fas fa-exclamation-circle"></i> Action requise</h3>
            <p>Merci de soumettre une photo d'identité conforme avant le 28 avril 2025</p>
        </div>

        <!-- Boutons d'action -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="registrationform.php" class="btn">Reprendre mon inscription</a>
            <a href="profile.php" class="btn btn-outline" style="margin-left: 10px;">Voir mon profil</a>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Animation simple de la barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                progressFill.style.width = '0';
                setTimeout(() => {
                    progressFill.style.width = '<?php echo $completion ?>%';
                    progressFill.style.transition = 'width 1s ease-in-out';
                }, 300);
            }
        });
    </script>
</body>
</html>