<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Étudiant</title>
  <link rel="stylesheet" href="style/studashboard.css">

</head>
<body>
  <div class="sidebar">
    <h2>🎓 Étudiant</h2>
    <a class="nav-link" href="#">🏠 Dashboard</a>
    <a class="nav-link" href="profile.html">👤 Mon Profil</a>
    <a class="nav-link" href="#">📝 Modifier Profil</a>
    <a class="nav-link" href="#">🔐 Déconnexion</a>
  </div>

  <a href="index.php" class="back-arrow">&#8592; Retour à l'accueil</a>


  <div class="main">
    <h1>Bienvenue, Elsie ! 👋</h1>

    <div class="status-bar">
      🟡 <strong>Statut :</strong> Votre fiche est en attente de validation par l’administration.
    </div>

    <div class="progress-container">
      <div class="progress-label">Progression de votre inscription :</div>
      <div class="progress-bar">
        <div class="progress">80%</div>
      </div>
    </div>

    <div class="card">
      <strong>Documents soumis :</strong><br/>
      ✔️ Pièce d'identité<br/>
      ❌ Photo d'identité<br/>
      ✔️ Justificatif de domicile<br/>
      ❌ Diplômes
    </div>

    <div class="card" style="border-left: 6px solid orange;">
      🔔 <strong>Notification :</strong><br/>
      Merci de soumettre une photo d’identité conforme avant le 28 avril.
    </div>

    <p style="font-size: 14px; color: gray;">Dernière modification de votre fiche : 14 avril 2025</p>

    <div class="options-container">
      <div class="option" onclick="alert('Affichage de la fiche personnelle')">
        <span>📄</span>
        Consulter ma fiche personnelle
      </div>

      <a href="registrationformtreatmt.php" class="option">
  <span>✏️</span>
  Modifier ma fiche d'inscription
</a>
    </div>
  </div>
</body>
</html>
