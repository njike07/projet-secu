<?php
// NJIKE Elsie
require_once 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant'){
    header("Location: pageLogin.php");
    exit();
}

$user = $_SESSION['user'];

// Récupérer la fiche d'inscription
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$fiche = $stmt->fetch();

// Récupérer les documents
$stmt = $pdo->prepare("SELECT type_document FROM documents WHERE fiche_id = ?");
$stmt->execute([$fiche['id'] ?? 0]);
$documents = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Étudiant</title>
  <link rel="stylesheet" href="style/studashboard.css">
  <link rel="stylesheet" href="style/stylestu.css">

</head>
<body>
  <div class="sidebar">
    <h2>🎓 Étudiant</h2>
    <a class="nav-link" href="#">🏠 Dashboard</a>
    <a class="nav-link" href="profile.php">👤 Mon Profil</a>
    <a class="nav-link" href="registrationform.php">📝 Modifier Profil</a>
  </div>

  <div class="top-navbar">
    <a href="student-home.php" class="back-arrow">&#8592; Retour à l'accueil</a>
    <div class="user-profile">
      <span class="user-name"><?php echo $user['prenom'] . ' ' . $user['nom'] ?></span>
      <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
    </div>
  </div>


  <div class="main">
    <h1>Bienvenue, <?php echo $user['prenom'] ?> ! 👋</h1>

    <div class="status-bar">
      <?php 
      $status_color = ['en_attente' => '🟡', 'validee' => '🟢', 'refusee' => '🔴'];
      echo $status_color[$fiche['statut'] ?? 'en_attente'];
      ?> <strong>Statut :</strong> <?php echo $fiche ? ucfirst(str_replace('_', ' ', $fiche['statut'])) : 'Fiche non créée' ?>
    </div>

    <div class="progress-container">
      <div class="progress-label">Progression de votre inscription :</div>
      <div class="progress-bar">
        <div class="progress"><?php echo $completion ?>%</div>
      </div>
    </div>

    <div class="card">
      <strong>Documents soumis :</strong><br/>
      <?php echo in_array('piece_identite', $documents) ? '✔️' : '❌' ?> Pièce d'identité<br/>
      <?php echo in_array('photo_identite', $documents) ? '✔️' : '❌' ?> Photo d'identité<br/>
      <?php echo in_array('justificatif_domicile', $documents) ? '✔️' : '❌' ?> Justificatif de domicile<br/>
      <?php echo in_array('diplomes', $documents) ? '✔️' : '❌' ?> Diplômes
    </div>

    <?php if ($fiche && $fiche['commentaires_admin']): ?>
    <div class="card" style="border-left: 6px solid orange;">
      🔔 <strong>Commentaire administrateur :</strong><br/>
      <?php echo htmlspecialchars($fiche['commentaires_admin']) ?>
    </div>
    <?php endif; ?>

    <p style="font-size: 14px; color: gray;">Dernière modification : <?php echo $fiche ? date('d/m/Y H:i', strtotime($fiche['date_derniere_modification'])) : 'Aucune' ?></p>

    <div class="options-container">
      <a href="profile.php" class="option">
        <span>📄</span>
        Consulter ma fiche personnelle
      </a>

      <a href="registrationform.php" class="option">
        <span>✏️</span>
        Modifier ma fiche d'inscription
      </a>
      
      <a href="documents.php" class="option">
        <span>📁</span>
        Gérer mes documents
      </a>
    </div>
  </div>
  <script src="js/student-dashboard.js"></script>
  <script>
    // Add smooth transitions
    document.addEventListener('DOMContentLoaded', function() {
      const options = document.querySelectorAll('.option');
      options.forEach(option => {
        option.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-3px)';
          this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        });
        option.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
        });
      });
      
      // Animate progress bar
      const progress = document.querySelector('.progress');
      if (progress) {
        const targetWidth = progress.textContent;
        progress.style.width = '0%';
        setTimeout(() => {
          progress.style.width = targetWidth;
          progress.style.transition = 'width 1s ease-in-out';
        }, 500);
      }
    });
  </script>
</body>
</html>