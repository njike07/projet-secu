<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload de Documents</title>
  <link rel="stylesheet" href="style/documents.css" />
</head>
<body>

  <?php include 'documTreatment.php'; ?>

  <a href="index.php" class="back-arrow">&#8592; Retour à l'accueil</a>

  <div class="container">
    <h1>Déposer mes documents</h1>

    <?php if (!empty($message)) echo $message; ?>

    <form action="" method="POST" enctype="multipart/form-data">
      <label>Pièce d'identité :</label>
      <input type="file" name="piece_identite" accept=".pdf,.jpg,.jpeg,.png" required />

      <label>Diplômes :</label>
      <input type="file" name="diplomes" accept=".pdf,.jpg,.jpeg,.png" required />

      <label>Photo d'identité :</label>
      <input type="file" name="photo_identite" accept=".jpg,.jpeg,.png" required />

      <label>Justificatif de domicile :</label>
      <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png" required />

      <button type="submit" name="submit">Envoyer les documents</button>
    </form>
  </div>
</body>
</html>
