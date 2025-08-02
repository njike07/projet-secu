<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$db = new Database();

// Vérification de l'authentification
if (!$auth->checkPermission('etudiant')) {
    header("Location: pageLogin.php?msg=Veuillez vous connecter&type=error");
    exit();
}

// Récupération des données existantes si disponibles
$stmt = $db->prepare("SELECT * FROM fiches_inscription WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$existingData = $stmt->fetch();
?>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription Étudiant</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body {
      background-color: #f1f1f1;
    }

    .card {
      background-color: white;
      padding: 30px;
      margin-top: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      color: #0b5394;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .submit-btn {
      background-color: #0b5394;
      color: white;
      font-size: 18px;
      padding: 10px 30px;
      border-radius: 8px;
    }

    .submit-btn:hover {
      background-color: #064a85;
    }

    .back-arrow {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #004080;
      color: white;
      font-size: 16px;
      font-weight: 500;
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s ease, transform 0.2s ease;
      z-index: 1000;
      font-family: sans-serif;
    }

    .back-arrow:hover {
      background-color: #0059b3;
      transform: translateY(-2px);
    }
  </style>
</head>
<body class="w3-container">

<a href="index.php" class="back-arrow">&#8592; Retour à l'accueil </a>

<div class="w3-content w3-card-4 card" style="max-width: 900px;">
  <h2 class="w3-center">Formulaire d'inscription Étudiant</h2>

  <form method="POST" action="registrationformtreatmt.php">
    <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">

    <!-- Informations personnelles -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Nom *</label>
        <input class="w3-input w3-border" name="nom" type="text" required value="<?php echo htmlspecialchars($existingData['nom'] ?? ''); ?>">
      </div>
      <div class="w3-half">
        <label>Prénom *</label>
        <input class="w3-input w3-border" name="prenom" type="text" required value="<?php echo htmlspecialchars($existingData['prenom'] ?? ''); ?>">
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Date de naissance *</label>
        <input class="w3-input w3-border" name="date_de_naissance" type="date" required value="<?php echo htmlspecialchars($existingData['date_de_naissance'] ?? ''); ?>">
      </div>
      <div class="w3-half">
        <label>Lieu de naissance *</label>
        <input class="w3-input w3-border" name="lieu_naissance" type="text" required value="<?php echo htmlspecialchars($existingData['lieu_naissance'] ?? ''); ?>">
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Sexe *</label>
        <select class="w3-select w3-border" name="sexe" required>
          <option value="" disabled <?php echo empty($existingData['sexe']) ? 'selected' : ''; ?>>Choisissez</option>
          <option value="Homme" <?php echo ($existingData['sexe'] ?? '') === 'Homme' ? 'selected' : ''; ?>>Homme</option>
          <option value="Femme" <?php echo ($existingData['sexe'] ?? '') === 'Femme' ? 'selected' : ''; ?>>Femme</option>
        </select>
      </div>
      <div class="w3-half">
        <label>Nationalité *</label>
        <input class="w3-input w3-border" name="nationalite" type="text" required value="<?php echo htmlspecialchars($existingData['nationalite'] ?? ''); ?>">
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Email *</label>
        <input class="w3-input w3-border" name="email" type="email" required value="<?php echo htmlspecialchars($existingData['email'] ?? $_SESSION['user_email'] ?? ''); ?>">
      </div>
      <div class="w3-half">
        <label>Téléphone *</label>
        <input class="w3-input w3-border" name="telephone" type="tel" required value="<?php echo htmlspecialchars($existingData['telephone'] ?? ''); ?>">
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-full">
        <label>Adresse postale *</label>
        <textarea class="w3-input w3-border" name="adresse_postale" rows="3" required placeholder="Adresse complète (rue, ville, code postal, pays)"><?php echo htmlspecialchars($existingData['adresse_postale'] ?? ''); ?></textarea>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Téléphone</label>
        <input class="w3-input w3-border" name="telephone" type="tel" required>
      </div>
      <div class="w3-half">
        <label>Mot de passe</label>
        <input class="w3-input w3-border" name="mot_de_passe" type="password" required>
      </div>
    </div>

    <!-- Informations académiques -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Dernier diplôme obtenu</label>
        <input class="w3-input w3-border" name="dernier_diplome_obtenue" type="text" required>
      </div>
      <div class="w3-half">
        <label>Établissement précédent</label>
        <input class="w3-input w3-border" name="etablisement_precedent" type="text" required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Formation demandée</label>
        <input class="w3-input w3-border" name="formation_demande" type="text" required>
      </div>
      <div class="w3-half">
        <label>Spécialisation</label>
        <input class="w3-input w3-border" name="specialisation" type="text" required>
      </div>
    </div>

    <!-- Contact d'urgence -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Nom contact urgence</label>
        <input class="w3-input w3-border" name="nom_contact_urgence" type="text" required>
      </div>
      <div class="w3-half">
        <label>Relation avec l'étudiant</label>
        <input class="w3-input w3-border" name="relation_etudiants" type="text" required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Téléphone contact urgence</label>
        <input class="w3-input w3-border" name="telephone_contact" type="tel" required>
      </div>
      <div class="w3-half">
        <label>Email contact urgence</label>
        <input class="w3-input w3-border" name="email_contact" type="email" required>
      </div>
    </div>

    <!-- Bouton de soumission -->
    <div class="w3-center w3-margin-top">
      <button class="w3-button submit-btn" type="submit">Soumettre l'inscription</button>
    </div>

  </form>
</div>

</body>
</html>
