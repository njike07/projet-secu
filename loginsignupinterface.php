<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion & Inscription</title>
  <link rel="stylesheet" href="style/loginSignup.css" />
</head>
<body>
  <div class="container">
    <h2>Inscription</h2>
    <form action="loginsignup.php" method="POST">
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <button type="submit" name="inscription">S'inscrire</button>
    </form>

    <h2>Connexion</h2>
    <form action="loginsignup.php" method="POST">
      <input type="email" name="email_connexion" placeholder="Email" required>
      <input type="password" name="mot_de_passe_connexion" placeholder="Mot de passe" required>
      <button type="submit" name="connexion">Se connecter</button>
    </form>

    <p style="color: red; text-align: center;">
      <?php if (isset($_GET['msg'])) echo htmlspecialchars($_GET['msg']); ?>
    </p>
  </div>
</body>
</html>
