<?php
// NJIKE Elsie
session_start();
require_once 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant'){
    header("Location: pageLogin.php");
    exit();
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$fiche = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mon Profil</title>
  <link rel="stylesheet" href="style/profile.css" />
</head>
<body>

  <a href="index.php" class="back-arrow">&#8592; Retour à l'accueil</a>

  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar"><?php echo substr($user['nom'],0,1) . substr($user['prenom'],0,1) ?></div>
        <h1><?php echo $user['prenom'] . ' ' . $user['nom'] ?></h1>
        <p><?php echo $fiche['formation_demandee'] ?? 'Formation non définie' ?></p>
      </div>

      <div class="profile-details">
        <?php if ($fiche): ?>
        <p><strong>Email:</strong> <?php echo $fiche['email'] ?></p>
        <p><strong>Téléphone:</strong> <?php echo $fiche['telephone'] ?></p>
        <p><strong>Date de naissance:</strong> <?php echo date('d/m/Y', strtotime($fiche['date_naissance'])) ?></p>
        <p><strong>Nationalité:</strong> <?php echo $fiche['nationalite'] ?></p>
        <p><strong>Statut:</strong> <?php echo ucfirst(str_replace('_', ' ', $fiche['statut'])) ?></p>
        <?php else: ?>
        <p>Aucune fiche d'inscription trouvée. <a href="registrationform.php">Créer ma fiche</a></p>
        <?php endif; ?>
      </div>
      
      <div class="profile-actions">
        <a href="registrationform.php" class="btn">Modifier mon profil</a>
      </div>
    </div>
  </div>


</body>
</html>
