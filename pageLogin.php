<?php
session_start();
require_once 'config/auth.php';

$auth = new Auth();
$message = '';
$messageType = 'error';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    // Validation CSRF
    if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de sécurité invalide';
    } else {
        $email = $_POST['email_connexion'] ?? '';
        $password = $_POST['mot_de_passe_connexion'] ?? '';
        $remember = isset($_POST['remember_me']);
        
        $result = $auth->login($email, $password, $remember);
        
        if ($result['success']) {
            $messageType = 'success';
            // Redirection selon le type d'utilisateur
            if ($result['user']['type'] === 'admin') {
                header("Location: admin_dashboard.php?msg=Connexion réussie");
            } else {
                header("Location: index.php?msg=Connexion réussie");
            }
            exit();
        } else {
            $message = $result['message'];
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
  <title>Connexion</title>
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
      text-align: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 5px;
      font-weight: 500;
    }
    .message.error {
      color: #d32f2f;
      background-color: #ffebee;
      border: 1px solid #ffcdd2;
    }
    .message.success {
      color: #388e3c;
      background-color: #e8f5e8;
      border: 1px solid #c8e6c9;
    }
    .message.info {
      color: #1976d2;
      background-color: #e3f2fd;
      border: 1px solid #bbdefb;
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

    <h2>Connexion</h2>
    <p class="welcome">Heureux de vous revoir ! Connectez-vous à votre espace COENDAI.</p>

    <form action="pageLogin.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
      <input type="email" name="email_connexion" placeholder="Email" required value="<?php echo isset($_POST['email_connexion']) ? htmlspecialchars($_POST['email_connexion']) : ''; ?>">
      <input type="password" name="mot_de_passe_connexion" placeholder="Mot de passe" required>
      <div style="margin: 10px 0; text-align: left;">
        <label style="font-size: 14px; color: #666;">
          <input type="checkbox" name="remember_me" style="margin-right: 5px;"> Se souvenir de moi
        </label>
      </div>
      <button type="submit" name="connexion">Se connecter</button>
    </form>

    <?php if ($message): ?>
    <div class="message <?php echo $messageType; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Connexion OAuth -->
    <div style="text-align: center; margin: 20px 0;">
      <p style="color: #666; margin: 15px 0;">Ou connectez-vous avec :</p>
      <div style="display: flex; gap: 10px; justify-content: center;">
        <?php 
        require_once 'config/oauth.php';
        $oauth = new OAuth();
        ?>
        <a href="<?php echo $oauth->getGoogleAuthUrl(); ?>" style="background: #db4437; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
          <i class="fab fa-google"></i> Google
        </a>
        <a href="<?php echo $oauth->getFacebookAuthUrl(); ?>" style="background: #4267B2; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
          <i class="fab fa-facebook-f"></i> Facebook
        </a>
      </div>
    </div>

    <div class="switch-link">
      Vous n'avez pas de compte ? <a href="pageSignup.php">Inscrivez-vous ici</a>
    </div>
  </div>
</body>
</html>
