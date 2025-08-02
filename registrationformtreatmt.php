<?php
session_start();
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$db = new Database();

// Vérification de l'authentification
if (!$auth->isLoggedIn()) {
    header("Location: pageLogin.php?msg=Veuillez vous connecter&type=error");
    exit();
}

try {

    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Validation CSRF
        if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token de sécurité invalide');
        }

        // Récupération et validation sécurisée des données
        $nom = Database::validateInput($_POST['nom'] ?? '');
        $prenom = Database::validateInput($_POST['prenom'] ?? '');
        $date_de_naissance = Database::validateInput($_POST['date_de_naissance'] ?? '');
        $lieu_naissance = Database::validateInput($_POST['lieu_naissance'] ?? '');
        $sexe = Database::validateInput($_POST['sexe'] ?? '');
        $nationalite = Database::validateInput($_POST['nationalite'] ?? '');
        $email = Database::validateInput($_POST['email'] ?? '', 'email');
        $telephone = Database::validateInput($_POST['telephone'] ?? '');
        $adresse_postale = Database::validateInput($_POST['adresse_postale'] ?? '');
        $dernier_diplome_obtenue = Database::validateInput($_POST['dernier_diplome_obtenue'] ?? '');
        $etablisement_precedent = Database::validateInput($_POST['etablisement_precedent'] ?? '');
        $formation_demande = Database::validateInput($_POST['formation_demande'] ?? '');
        $specialisation = Database::validateInput($_POST['specialisation'] ?? '');
        $nom_contact_urgence = Database::validateInput($_POST['nom_contact_urgence'] ?? '');
        $relation_etudiants = Database::validateInput($_POST['relation_etudiants'] ?? '');
        $telephone_contact = Database::validateInput($_POST['telephone_contact'] ?? '');
        $email_contact = Database::validateInput($_POST['email_contact'] ?? '', 'email');
        
        // Validation des champs obligatoires
        if (!$nom || !$prenom || !$email || !$date_de_naissance) {
            throw new Exception('Tous les champs obligatoires doivent être remplis');
        }

        // Vérifier si une fiche existe déjà pour cet utilisateur
        $stmt = $db->prepare("SELECT id FROM fiches_inscription WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $existingFiche = $stmt->fetch();
        
        if ($existingFiche) {
            // Mise à jour de la fiche existante
            $sql = "UPDATE fiches_inscription SET 
                nom = ?, prenom = ?, date_de_naissance = ?, lieu_naissance = ?, sexe = ?, 
                nationalite = ?, email = ?, telephone = ?, adresse_postale = ?,
                dernier_diplome_obtenue = ?, etablisement_precedent = ?, formation_demande = ?, 
                specialisation = ?, nom_contact_urgence = ?, relation_etudiants = ?, 
                telephone_contact = ?, email_contact = ?, date_derniere_modification = NOW()
                WHERE user_id = ?";
            
            $stmt = $db->prepare($sql);
            $params = [$nom, $prenom, $date_de_naissance, $lieu_naissance, $sexe, $nationalite, 
                      $email, $telephone, $adresse_postale, $dernier_diplome_obtenue, 
                      $etablisement_precedent, $formation_demande, $specialisation, 
                      $nom_contact_urgence, $relation_etudiants, $telephone_contact, 
                      $email_contact, $_SESSION['user_id']];
        } else {
            // Création d'une nouvelle fiche
            $sql = "INSERT INTO fiches_inscription (user_id, nom, prenom, date_de_naissance, lieu_naissance, sexe, 
                nationalite, email, telephone, adresse_postale, dernier_diplome_obtenue, etablisement_precedent, 
                formation_demande, specialisation, nom_contact_urgence, relation_etudiants, 
                telephone_contact, email_contact, statut_inscription, date_soumission)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NOW())";
            
            $stmt = $db->prepare($sql);
            $params = [$_SESSION['user_id'], $nom, $prenom, $date_de_naissance, $lieu_naissance, 
                      $sexe, $nationalite, $email, $telephone, $adresse_postale, 
                      $dernier_diplome_obtenue, $etablisement_precedent, $formation_demande, 
                      $specialisation, $nom_contact_urgence, $relation_etudiants, 
                      $telephone_contact, $email_contact];
        }

        // Exécuter la requête
        if ($stmt->execute($params)) {
            // Journalisation
            $action = $existingFiche ? 'modification_fiche' : 'creation_fiche';
            $details = $existingFiche ? 'Modification de la fiche d\'inscription' : 'Création de la fiche d\'inscription';
            
            $logStmt = $db->prepare("INSERT INTO modifications (user_id, action, details, ip_address, user_agent, modification_time) VALUES (?, ?, ?, ?, ?, NOW())");
            $logStmt->execute([
                $_SESSION['user_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            $message = $existingFiche ? 'Fiche mise à jour avec succès!' : 'Inscription réussie! Votre fiche a été créée.';
            header("Location: index.php?msg=" . urlencode($message) . "&type=success");
            exit();
        } else {
            throw new Exception('Erreur lors de l\'enregistrement en base de données');
        }
    }

} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>Erreur de connexion à la base de données: " . $e->getMessage() . "</div>";
}
?>

