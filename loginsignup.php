<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kamerhosting_amn', 'kamerhosting_amn', 'RStLrbpGNPOq');

// ======= Inscription =======
if (isset($_POST['inscription'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        header("Location: loginsignupinterface.php?msg=Email déjà utilisé");
        exit();
    }

    // Insérer dans la table compte
    $insert = $pdo->prepare("INSERT INTO compte (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
    $insert->execute([$nom, $prenom, $email, $mot_de_passe]);

    if ($insert) {
        header("Location: loginsignupinterface.php?msg=Inscription réussie !, veillez vous connecter");
    } else {
        header("Location: loginsignupinterface.php?msg=Erreur lors de l'inscription.");
    }
    exit();
}

// ======= Connexion =======
if (isset($_POST['connexion'])) {
    $email = $_POST['email_connexion'];
    $mot_de_passe = $_POST['mot_de_passe_connexion'];

    $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
       $_SESSION['compte'] = $user;
        header("Location: index.php?msg=Connexion réussie !");
    } else {
        header("Location: loginsignupinterface.php?msg=Email ou mot de passe incorrect.");
    }
    exit();
}
?>
