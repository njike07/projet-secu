<?php
$message = "";

try {
    $db = new PDO('mysql:host=localhost;dbname=kamerhosting_amn', 'kamerhosting_amn', 'RStLrbpGNPOq');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_POST['submit'])) {
    $pieceIdentite = $_FILES['piece_identite']['name'];
    $diplomes = $_FILES['diplomes']['name'];
    $photoIdentite = $_FILES['photo_identite']['name'];
    $justificatif = $_FILES['justificatif']['name'];

    $targetPiece = $targetDir . basename($pieceIdentite);
    $targetDiplomes = $targetDir . basename($diplomes);
    $targetPhoto = $targetDir . basename($photoIdentite);
    $targetJustif = $targetDir . basename($justificatif);

    $success1 = move_uploaded_file($_FILES['piece_identite']['tmp_name'], $targetPiece);
    $success2 = move_uploaded_file($_FILES['diplomes']['tmp_name'], $targetDiplomes);
    $success3 = move_uploaded_file($_FILES['photo_identite']['tmp_name'], $targetPhoto);
    $success4 = move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetJustif);

    if ($success1 && $success2 && $success3 && $success4) {
        $stmt = $db->prepare("INSERT INTO documents (piece_identite, diplomes, photo_identite, justificatif_de_domicile) VALUES (?, ?, ?, ?)");
        $insert = $stmt->execute([
            $targetPiece,
            $targetDiplomes,
            $targetPhoto,
            $targetJustif
        ]);

        if ($insert) {
            $message = "<p class='success'>✅ Vos documents ont été envoyés avec succès.</p>";
        } else {
            $message = "<p class='error'>❌ Erreur lors de l'enregistrement dans la base de données.</p>";
        }
    } else {
        $message = "<p class='error'>❌ Erreur lors du téléchargement des fichiers.</p>";
    }
}
?>
