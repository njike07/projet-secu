<?php
session_start();
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

  <div class="background-animation"></div>
<!--
  <div class="toggle-theme">
    <button id="themeToggle">🌙 Mode sombre</button>
  </div>
  -->

  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <img src="images/user-avatar.png" alt="Photo de profil" class="avatar">
        <?php echo substr($_SESSION['compte']['nom'],0,1 ) ?>
        <h1> <?php echo $_SESSION['fiches_inscription']['specialisation'] ?></h1>
      </div>

      <div class="profile-details">
      <h1> Nom  : <?php echo $_SESSION['compte']['nom'] ?></h1>
      <h1> Prenom  : <?php echo $_SESSION['compte']['prenom'] ?></h1>
      <h1> Email :  <?php echo $_SESSION['compte']['email'] ?></h1>
      <h1> Email :  ****</h1>
      
      </div>
      <div class="profile-actions">
        <a href="#" class="btn">Modifier mon profil</a>
      </div>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('themeToggle');
    toggleBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      if (document.body.classList.contains('dark')) {
        toggleBtn.textContent = '☀️ Mode clair';
      } else {
        toggleBtn.textContent = '🌙 Mode sombre';
      }
    });
  </script>
</body>
</html>
