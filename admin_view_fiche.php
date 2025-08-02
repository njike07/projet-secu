<?php
session_start();
require_once 'config.php';
require_once 'admin_functions.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur'){
    header("Location: pageLogin.php");
    exit();
}

$fiche_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT f.*, u.email as user_email FROM fiches_inscription f JOIN utilisateurs u ON f.utilisateur_id = u.id WHERE f.id = ?");
$stmt->execute([$fiche_id]);
$fiche = $stmt->fetch();

if (!$fiche) {
    header("Location: admindash.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statut = $_POST['statut'];
    $commentaire = sanitize($_POST['commentaire']);
    updateFicheStatus($pdo, $fiche_id, $statut, $commentaire);
    header("Location: admindash.php?msg=Statut mis à jour");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'inscription - <?php echo $fiche['nom'] . ' ' . $fiche['prenom'] ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body { background-color: #f5f5f5; font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; color: #0b5394; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #0b5394; border-bottom: 2px solid #0b5394; padding-bottom: 5px; }
        .field { margin-bottom: 10px; }
        .field strong { display: inline-block; width: 200px; }
        .status-form { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .btn { background: #0b5394; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #064a85; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fiche d'inscription</h1>
            <h2><?php echo $fiche['nom'] . ' ' . $fiche['prenom'] ?></h2>
            <p>Statut actuel: <strong><?php echo ucfirst(str_replace('_', ' ', $fiche['statut'])) ?></strong></p>
        </div>

        <div class="section">
            <h3>Informations personnelles</h3>
            <div class="field"><strong>Nom:</strong> <?php echo $fiche['nom'] ?></div>
            <div class="field"><strong>Prénom:</strong> <?php echo $fiche['prenom'] ?></div>
            <div class="field"><strong>Date de naissance:</strong> <?php echo date('d/m/Y', strtotime($fiche['date_naissance'])) ?></div>
            <div class="field"><strong>Lieu de naissance:</strong> <?php echo $fiche['lieu_naissance'] ?></div>
            <div class="field"><strong>Sexe:</strong> <?php echo $fiche['sexe'] ?></div>
            <div class="field"><strong>Nationalité:</strong> <?php echo $fiche['nationalite'] ?></div>
            <div class="field"><strong>Email:</strong> <?php echo $fiche['email'] ?></div>
            <div class="field"><strong>Téléphone:</strong> <?php echo $fiche['telephone'] ?></div>
            <div class="field"><strong>Adresse:</strong> <?php echo $fiche['adresse_postale'] ?></div>
        </div>

        <div class="section">
            <h3>Informations académiques</h3>
            <div class="field"><strong>Dernier diplôme:</strong> <?php echo $fiche['dernier_diplome'] ?></div>
            <div class="field"><strong>Établissement précédent:</strong> <?php echo $fiche['etablissement_precedent'] ?></div>
            <div class="field"><strong>Formation demandée:</strong> <?php echo $fiche['formation_demandee'] ?></div>
            <div class="field"><strong>Spécialisation:</strong> <?php echo $fiche['specialisation'] ?></div>
        </div>

        <div class="section">
            <h3>Contact d'urgence</h3>
            <div class="field"><strong>Nom:</strong> <?php echo $fiche['nom_contact_urgence'] ?></div>
            <div class="field"><strong>Relation:</strong> <?php echo $fiche['relation_contact'] ?></div>
            <div class="field"><strong>Téléphone:</strong> <?php echo $fiche['telephone_contact'] ?></div>
            <div class="field"><strong>Email:</strong> <?php echo $fiche['email_contact'] ?></div>
        </div>

        <div class="status-form">
            <h3>Gestion du statut</h3>
            <form method="POST">
                <p><strong>Statut actuel:</strong> <?php echo ucfirst(str_replace('_', ' ', $fiche['statut'])) ?></p>
                <p>
                    <label><input type="radio" name="statut" value="en_attente" <?php echo $fiche['statut'] === 'en_attente' ? 'checked' : '' ?>> En attente</label><br>
                    <label><input type="radio" name="statut" value="validee" <?php echo $fiche['statut'] === 'validee' ? 'checked' : '' ?>> Validée</label><br>
                    <label><input type="radio" name="statut" value="refusee" <?php echo $fiche['statut'] === 'refusee' ? 'checked' : '' ?>> Refusée</label>
                </p>
                <p>
                    <label>Commentaire administrateur:</label><br>
                    <textarea name="commentaire" rows="3" style="width: 100%; padding: 8px;"><?php echo $fiche['commentaires_admin'] ?></textarea>
                </p>
                <button type="submit" class="btn">Mettre à jour le statut</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="admindash.php" class="btn">Retour au tableau de bord</a>
            <a href="admin_edit_fiche.php?id=<?php echo $fiche['id'] ?>" class="btn">Modifier la fiche</a>
        </div>
    </div>
</body>
</html>