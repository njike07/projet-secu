<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kamerhosting_amn', 'kamerhosting_amn', 'RStLrbpGNPOq');

// Inscription
if (isset($_POST['inscription'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        header("Location: pageSignup.php?msg=Email déjà utilisé");
        exit();
    }

    $insert = $pdo->prepare("INSERT INTO compte (nom, prenom, email, mot_de_passe, type) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$nom, $prenom, $email, $mot_de_passe, $type]);

    if ($insert) {
        header("Location: pageLogin.php?msg=Inscription réussie ! Veuillez vous connecter.");
    } else {
        header("Location: pageSignup.php?msg=Erreur lors de l'inscription.");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inscription</title>
  <link rel="stylesheet" href="style/loginSignup.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #1e3c72, #2a5298);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 450px;
    }
    .header {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo {
      width: 80px;
      height: 80px;
      background-color: #ccc;
      border-radius: 50%;
      margin: 0 auto 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: #333;
    }
    .ecole {
      font-size: 22px;
      font-weight: bold;
      color: #1e3c72;
    }
    h2 {
      margin-bottom: 10px;
      color: #1e3c72;
      text-align: center;
    }
    .welcome {
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
      color: #444;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #1e3c72;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #16325c;
    }
    .message {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .switch-link {
      text-align: center;
      margin-top: 10px;
    }
    .switch-link a {
      color: #1e3c72;
      text-decoration: none;
      font-weight: bold;
    }
    .switch-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo">🎓</div>
      <div class="ecole">COENDAI</div>
    </div>

    <h2>Inscription</h2>
    <p class="welcome">Bienvenue chez COENDAI ! Inscrivez-vous pour continuer.</p>

    <form action="pageSignup.php" method="POST">
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <button type="submit" name="inscription">S'inscrire</button>
    </form>

    <p class="message">
      <?php if (isset($_GET['msg'])) echo htmlspecialchars($_GET['msg']); ?>
    </p>

    <div class="switch-link">
      Vous avez déjà un compte ? <a href="pageLogin.php">Connectez-vous ici</a>
    </div>
  </div>
</body>
</html>
