<?php
session_start();
require_once 'config.php';
require_once 'admin_functions.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur'){
    header("Location: pageLogin.php");
    exit();
}

$fiche_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM fiches_inscription WHERE id = ?");
$stmt->execute([$fiche_id]);
$fiche = $stmt->fetch();

if (!$fiche) {
    header("Location: admindash.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => sanitize($_POST['nom']),
        'prenom' => sanitize($_POST['prenom']),
        'date_naissance' => $_POST['date_naissance'],
        'lieu_naissance' => sanitize($_POST['lieu_naissance']),
        'sexe' => $_POST['sexe'],
        'nationalite' => sanitize($_POST['nationalite']),
        'adresse_postale' => sanitize($_POST['adresse_postale']),
        'email' => sanitize($_POST['email']),
        'telephone' => sanitize($_POST['telephone']),
        'dernier_diplome' => sanitize($_POST['dernier_diplome']),
        'etablissement_precedent' => sanitize($_POST['etablissement_precedent']),
        'formation_demandee' => sanitize($_POST['formation_demandee']),
        'specialisation' => sanitize($_POST['specialisation']),
        'nom_contact_urgence' => sanitize($_POST['nom_contact_urgence']),
        'relation_contact' => sanitize($_POST['relation_contact']),
        'telephone_contact' => sanitize($_POST['telephone_contact']),
        'email_contact' => sanitize($_POST['email_contact'])
    ];
    
    $updates = [];
    $values = [];
    foreach ($data as $field => $value) {
        if ($fiche[$field] !== $value) {
            $updates[] = "$field = ?";
            $values[] = $value;
            logModification($pdo, $fiche_id, $_SESSION['user']['id'], 'modification', $field, $fiche[$field], $value);
        }
    }
    
    if (!empty($updates)) {
        $values[] = $fiche_id;
        $sql = "UPDATE fiches_inscription SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
    }
    
    header("Location: admin_view_fiche.php?id=$fiche_id&msg=Fiche mise à jour");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la fiche</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body { background-color: #f1f1f1; }
        .card { background-color: white; padding: 30px; margin: 20px auto; max-width: 900px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { color: #0b5394; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .submit-btn { background-color: #0b5394; color: white; padding: 10px 30px; border-radius: 8px; border: none; }
        .submit-btn:hover { background-color: #064a85; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Modifier la fiche d'inscription</h2>
        
        <form method="POST">
            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Nom</label>
                    <input class="w3-input w3-border" name="nom" type="text" value="<?php echo $fiche['nom'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Prénom</label>
                    <input class="w3-input w3-border" name="prenom" type="text" value="<?php echo $fiche['prenom'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Date de naissance</label>
                    <input class="w3-input w3-border" name="date_naissance" type="date" value="<?php echo $fiche['date_naissance'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Lieu de naissance</label>
                    <input class="w3-input w3-border" name="lieu_naissance" type="text" value="<?php echo $fiche['lieu_naissance'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Sexe</label>
                    <select class="w3-select w3-border" name="sexe" required>
                        <option value="Homme" <?php echo $fiche['sexe'] === 'Homme' ? 'selected' : '' ?>>Homme</option>
                        <option value="Femme" <?php echo $fiche['sexe'] === 'Femme' ? 'selected' : '' ?>>Femme</option>
                    </select>
                </div>
                <div class="w3-half">
                    <label>Nationalité</label>
                    <input class="w3-input w3-border" name="nationalite" type="text" value="<?php echo $fiche['nationalite'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Email</label>
                    <input class="w3-input w3-border" name="email" type="email" value="<?php echo $fiche['email'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Téléphone</label>
                    <input class="w3-input w3-border" name="telephone" type="tel" value="<?php echo $fiche['telephone'] ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse postale</label>
                <textarea class="w3-input w3-border" name="adresse_postale" required><?php echo $fiche['adresse_postale'] ?></textarea>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Dernier diplôme</label>
                    <input class="w3-input w3-border" name="dernier_diplome" type="text" value="<?php echo $fiche['dernier_diplome'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Établissement précédent</label>
                    <input class="w3-input w3-border" name="etablissement_precedent" type="text" value="<?php echo $fiche['etablissement_precedent'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Formation demandée</label>
                    <input class="w3-input w3-border" name="formation_demandee" type="text" value="<?php echo $fiche['formation_demandee'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Spécialisation</label>
                    <input class="w3-input w3-border" name="specialisation" type="text" value="<?php echo $fiche['specialisation'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Nom contact urgence</label>
                    <input class="w3-input w3-border" name="nom_contact_urgence" type="text" value="<?php echo $fiche['nom_contact_urgence'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Relation</label>
                    <input class="w3-input w3-border" name="relation_contact" type="text" value="<?php echo $fiche['relation_contact'] ?>" required>
                </div>
            </div>

            <div class="w3-row-padding form-group">
                <div class="w3-half">
                    <label>Téléphone contact</label>
                    <input class="w3-input w3-border" name="telephone_contact" type="tel" value="<?php echo $fiche['telephone_contact'] ?>" required>
                </div>
                <div class="w3-half">
                    <label>Email contact</label>
                    <input class="w3-input w3-border" name="email_contact" type="email" value="<?php echo $fiche['email_contact'] ?>" required>
                </div>
            </div>

            <div class="w3-center">
                <button class="submit-btn" type="submit">Mettre à jour la fiche</button>
                <a href="admin_view_fiche.php?id=<?php echo $fiche['id'] ?>" class="w3-button w3-grey" style="margin-left: 10px;">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>