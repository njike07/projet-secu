<?php
// NJIKE Elsie
require_once 'config.php';

// Connexion
if (isset($_POST['connexion'])) {
    // Validation CSRF
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        header("Location: pageLogin.php?msg=Erreur de sécurité. Veuillez réessayer.");
        exit();
    }
    
    $email = sanitize($_POST['email_connexion']);
    $mot_de_passe = $_POST['mot_de_passe_connexion'];
    
    // Vérification protection brute force
    if (checkBruteForce($pdo, $email)) {
        logTentativeConnexion($pdo, $email, 0);
        header("Location: pageLogin.php?msg=Trop de tentatives. Veuillez attendre 15 minutes.");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Régénérer l'ID de session pour éviter la fixation
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        
        // Mettre à jour dernière connexion
        $stmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        logTentativeConnexion($pdo, $email, 1);
        
        if ($user['role'] === 'administrateur') {
            header("Location: admindash.php");
        } else {
            header("Location: student-home.php");
        }
    } else {
        logTentativeConnexion($pdo, $email, 0);
        header("Location: pageLogin.php?msg=Email ou mot de passe incorrect.");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 380px;
    }
    .header {
      text-align: center;
      margin-bottom: 15px;
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
      margin-bottom: 15px;
      font-size: 13px;
      color: #444;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #1e3c72;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
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
    .divider {
      text-align: center;
      margin: 15px 0;
      position: relative;
    }
    .divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: #ddd;
    }
    .divider span {
      background: white;
      padding: 0 15px;
      color: #666;
      font-size: 14px;
    }
    .social-buttons {
      margin-bottom: 15px;
    }
    .social-btn {
      width: 100%;
      padding: 8px;
      margin-bottom: 8px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s;
    }
    .google-btn {
      background: white;
      color: #333;
    }
    .google-btn:hover {
      background: #f8f9fa;
      border-color: #dadce0;
    }
    .facebook-btn {
      background: #1877f2;
      color: white;
      border-color: #1877f2;
    }
    .facebook-btn:hover {
      background: #166fe5;
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
      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
      <input type="email" name="email_connexion" placeholder="Email" required>
      <input type="password" name="mot_de_passe_connexion" placeholder="Mot de passe" required>
      <button type="submit" name="connexion">Se connecter</button>
    </form>

    <div class="divider">
      <span>ou</span>
    </div>

    <div class="social-buttons">
      <button type="button" class="social-btn google-btn">
        <i class="fab fa-google"></i>
        Continuer avec Google
      </button>
      <button type="button" class="social-btn facebook-btn">
        <i class="fab fa-facebook-f"></i>
        Continuer avec Facebook
      </button>
    </div>

    <p class="message">
      <?php if (isset($_GET['msg'])) echo htmlspecialchars($_GET['msg']); ?>
    </p>

    <div class="switch-link">
      <a href="index.php" style="color: #666; font-size: 12px;">← Retour à l'accueil</a><br><br>
      Vous n'avez pas de compte ? <a href="pageSignup.php">Créer un compte étudiant</a><br>
      <small>Admin test: admin@etablissement.com / password</small>
    </div>
  </div>
  <script src="js/scriptregistration.js"></script>
  <script src="js/social-auth.js"></script>
</body>
</html>
