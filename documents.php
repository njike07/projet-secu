<?php
// NJIKE Elsie
session_start();
require_once 'config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant'){
    header("Location: pageLogin.php");
    exit();
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$fiche = $stmt->fetch();

if (!$fiche) {
    header("Location: registrationform.php?msg=Créez d'abord votre fiche d'inscription");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $upload_dir = 'uploads/';
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    
    foreach (['piece_identite', 'diplomes', 'photo_identite', 'justificatif_domicile'] as $doc_type) {
        if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] === 0) {
            $file = $_FILES[$doc_type];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed_types) && $file['size'] <= 5000000) {
                $filename = $doc_type . '_' . $fiche['id'] . '_' . time() . '.' . $ext;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $stmt = $pdo->prepare("INSERT INTO documents (fiche_id, type_document, nom_fichier, chemin_fichier, taille_fichier, type_mime) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$fiche['id'], $doc_type, $file['name'], $filepath, $file['size'], $file['type']]);
                }
            }
        }
    }
    $message = '<div style="color: green;">Documents uploadés avec succès!</div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload de Documents</title>
  <link rel="stylesheet" href="style/documents.css" />
</head>
<body>

  <a href="index.php" class="back-arrow">&#8592; Retour à l'accueil</a>

  <div class="container">
    <h1>Déposer mes documents</h1>

    <?php echo $message; ?>

    <form action="" method="POST" enctype="multipart/form-data">
      <label>Pièce d'identité :</label>
      <input type="file" name="piece_identite" accept=".pdf,.jpg,.jpeg,.png" />

      <label>Diplômes :</label>
      <input type="file" name="diplomes" accept=".pdf,.jpg,.jpeg,.png" />

      <label>Photo d'identité :</label>
      <input type="file" name="photo_identite" accept=".jpg,.jpeg,.png" />

      <label>Justificatif de domicile :</label>
      <input type="file" name="justificatif_domicile" accept=".pdf,.jpg,.jpeg,.png" />

      <button type="submit" name="submit">Envoyer les documents</button>
    </form>
  </div>
</body>
</html>
