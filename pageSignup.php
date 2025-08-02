<?php
session_start();
require_once 'config/auth.php';

$auth = new Auth();
$message = '';
$messageType = 'error';

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscription'])) {
    // Validation CSRF
    if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de sécurité invalide';
    } else {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['mot_de_passe'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $type = $_POST['type'] ?? 'etudiant';
        
        // Validation des mots de passe
        if ($password !== $confirmPassword) {
            $message = 'Les mots de passe ne correspondent pas';
        } else {
            $result = $auth->register($nom, $prenom, $email, $password, $type);
            
            if ($result['success']) {
                header("Location: pageLogin.php?msg=Inscription réussie ! Vous pouvez maintenant vous connecter.&type=success");
                exit();
            } else {
                $message = $result['message'];
            }
        }
    }
}

// Récupération du message depuis l'URL
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
    $messageType = isset($_GET['type']) ? $_GET['type'] : 'info';
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
      <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
      <input type="text" name="nom" placeholder="Nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
      <input type="text" name="prenom" placeholder="Prénom" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
      <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
      <div style="margin: 10px 0; text-align: left; font-size: 12px; color: #666;">
        Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
      </div>
      <button type="submit" name="inscription">S'inscrire</button>
    </form>

    <?php if ($message): ?>
    <div class="message <?php echo $messageType; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="switch-link">
      Vous avez déjà un compte ? <a href="pageLogin.php">Connectez-vous ici</a>
    </div>
  </div>
</body>
</html>
