<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant'){
    header("Location: pageLogin.php");
    exit();
}

$user = $_SESSION['user'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => sanitize($_POST['nom']),
        'prenom' => sanitize($_POST['prenom']),
        'date_naissance' => $_POST['date_de_naissance'],
        'lieu_naissance' => sanitize($_POST['lieu_naissance'] ?? ''),
        'sexe' => $_POST['sexe'],
        'nationalite' => sanitize($_POST['nationalite']),
        'adresse_postale' => sanitize($_POST['adresse_postale'] ?? ''),
        'email' => sanitize($_POST['email']),
        'telephone' => sanitize($_POST['telephone']),
        'dernier_diplome' => sanitize($_POST['dernier_diplome_obtenue']),
        'etablissement_precedent' => sanitize($_POST['etablisement_precedent']),
        'formation_demandee' => sanitize($_POST['formation_demande']),
        'specialisation' => sanitize($_POST['specialisation']),
        'nom_contact_urgence' => sanitize($_POST['nom_contact_urgence']),
        'relation_contact' => sanitize($_POST['relation_etudiants']),
        'telephone_contact' => sanitize($_POST['telephone_contact']),
        'email_contact' => sanitize($_POST['email_contact'])
    ];
    
    // Vérifier si une fiche existe déjà
    $stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
    $stmt->execute([$user['id']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mise à jour (seulement les champs modifiables)
        $modifiable = ['adresse_postale', 'email', 'telephone', 'nom_contact_urgence', 'relation_contact', 'telephone_contact', 'email_contact'];
        $updates = [];
        $values = [];
        
        foreach ($modifiable as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $values[] = $data[$field];
                
                // Log modification
                if ($existing[$field] !== $data[$field]) {
                    logModification($pdo, $existing['id'], $user['id'], 'modification', $field, $existing[$field], $data[$field]);
                }
            }
        }
        
        if (!empty($updates)) {
            $values[] = $user['id'];
            $sql = "UPDATE fiches_inscription SET " . implode(', ', $updates) . " WHERE utilisateur_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
        }
        
        header("Location: index.php?msg=Fiche mise à jour avec succès");
    } else {
        // Création nouvelle fiche
        $data['utilisateur_id'] = $user['id'];
        
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $stmt = $pdo->prepare("INSERT INTO fiches_inscription ($fields) VALUES ($placeholders)");
        $stmt->execute($data);
        
        $fiche_id = $pdo->lastInsertId();
        logModification($pdo, $fiche_id, $user['id'], 'creation');
        
        header("Location: index.php?msg=Fiche créée avec succès");
    }
    exit();
}

// Récupérer les données existantes
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$fiche = $stmt->fetch();
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

  <form method="POST" action="">

    <!-- Informations personnelles -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Nom</label>
        <input class="w3-input w3-border" name="nom" type="text" value="<?php echo $fiche['nom'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
      <div class="w3-half">
        <label>Prénom</label>
        <input class="w3-input w3-border" name="prenom" type="text" value="<?php echo $fiche['prenom'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Date de naissance</label>
        <input class="w3-input w3-border" name="date_de_naissance" type="date" value="<?php echo $fiche['date_naissance'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
      <div class="w3-half">
        <label>Lieu de naissance</label>
        <input class="w3-input w3-border" name="lieu_naissance" type="text" value="<?php echo $fiche['lieu_naissance'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Sexe</label>
        <select class="w3-select w3-border" name="sexe" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'disabled' : '' ?> required>
          <option value="" disabled>Choisissez</option>
          <option value="Homme" <?php echo ($fiche['sexe'] ?? '') === 'Homme' ? 'selected' : '' ?>>Homme</option>
          <option value="Femme" <?php echo ($fiche['sexe'] ?? '') === 'Femme' ? 'selected' : '' ?>>Femme</option>
        </select>
      </div>
      <div class="w3-half">
        <label>Nationalité</label>
        <input class="w3-input w3-border" name="nationalite" type="text" value="<?php echo $fiche['nationalite'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Adresse postale</label>
        <textarea class="w3-input w3-border" name="adresse_postale" required><?php echo $fiche['adresse_postale'] ?? '' ?></textarea>
      </div>
      <div class="w3-half">
        <label>Email</label>
        <input class="w3-input w3-border" name="email" type="email" value="<?php echo $fiche['email'] ?? $user['email'] ?>" required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Téléphone</label>
        <input class="w3-input w3-border" name="telephone" type="tel" value="<?php echo $fiche['telephone'] ?? '' ?>" required>
      </div>
    </div>

    <!-- Informations académiques -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Dernier diplôme obtenu</label>
        <input class="w3-input w3-border" name="dernier_diplome_obtenue" type="text" value="<?php echo $fiche['dernier_diplome'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
      <div class="w3-half">
        <label>Établissement précédent</label>
        <input class="w3-input w3-border" name="etablisement_precedent" type="text" value="<?php echo $fiche['etablissement_precedent'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Formation demandée</label>
        <input class="w3-input w3-border" name="formation_demande" type="text" value="<?php echo $fiche['formation_demandee'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
      <div class="w3-half">
        <label>Spécialisation</label>
        <input class="w3-input w3-border" name="specialisation" type="text" value="<?php echo $fiche['specialisation'] ?? '' ?>" <?php echo $fiche && $fiche['statut'] === 'validee' ? 'readonly' : '' ?> required>
      </div>
    </div>

    <!-- Contact d'urgence -->
    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Nom contact urgence</label>
        <input class="w3-input w3-border" name="nom_contact_urgence" type="text" value="<?php echo $fiche['nom_contact_urgence'] ?? '' ?>" required>
      </div>
      <div class="w3-half">
        <label>Relation avec l'étudiant</label>
        <input class="w3-input w3-border" name="relation_etudiants" type="text" value="<?php echo $fiche['relation_contact'] ?? '' ?>" required>
      </div>
    </div>

    <div class="w3-row-padding form-group">
      <div class="w3-half">
        <label>Téléphone contact urgence</label>
        <input class="w3-input w3-border" name="telephone_contact" type="tel" value="<?php echo $fiche['telephone_contact'] ?? '' ?>" required>
      </div>
      <div class="w3-half">
        <label>Email contact urgence</label>
        <input class="w3-input w3-border" name="email_contact" type="email" value="<?php echo $fiche['email_contact'] ?? '' ?>" required>
      </div>
    </div>

    <!-- Bouton de soumission -->
    <div class="w3-center w3-margin-top">
      <button class="w3-button submit-btn" type="submit"><?php echo $fiche ? 'Mettre à jour' : 'Soumettre' ?> l'inscription</button>
    </div>

  </form>
</div>

</body>
</html>