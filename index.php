<?php
session_start();
//var_dump($_SESSION);

if(!$_SESSION['compte']){
    header("Location: pageLogin.php?msg=Veillez vous connecter !");
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
              <?php echo substr($_SESSION['compte']['nom'],0,1 ) ?>
              <?php echo substr($_SESSION['compte']['prenom'],0,1 ) ?>

            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <!-- Carte de bienvenue -->
        <div class="welcome-card">
            <h1>Bienvenue, <?php echo $_SESSION['compte']['prenom'] ?></h1>
            <p>Votre portail étudiant pour gérer votre inscription</p>
            <div class="status-badge">Statut : En attente de validation</div>
        </div>

        <!-- Progression -->
        <div class="progress-container">
            <h3 class="progress-title">Progression de votre inscription</h3>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="progress-text">80% complété</div>
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

    <script>
        // Animation simple de la barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressFill = document.querySelector('.progress-fill');
            progressFill.style.width = '0';
            setTimeout(() => {
                progressFill.style.width = '80%';
                progressFill.style.transition = 'width 1s ease-in-out';
            }, 300);
        });
    </script>
</body>
</html>