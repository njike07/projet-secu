<?php
session_start();
try {
    // Connexion à la base de données avec PDO
    $database = new PDO('mysql:host=localhost;dbname=inscription', 'root', '');

    // Définir le mode de gestion des erreurs
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Récupérer les données du formulaire
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $date_de_naissance = $_POST['date_de_naissance'];
        $sexe = $_POST['sexe'];
        $nationalite = $_POST['nationalite'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hachage du mot de passe
        $dernier_diplome_obtenue = $_POST['dernier_diplome_obtenue'];
        $etablisement_precedent = $_POST['etablisement_precedent'];
        $formation_demande = $_POST['formation_demande'];
        $specialisation = $_POST['specialisation'];
        $nom_contact_urgence = $_POST['nom_contact_urgence'];
        $relation_etudiants = $_POST['relation_etudiants'];
        $telephone_contact = $_POST['telephone_contact'];
        $email_contact = $_POST['email_contact'];

        // Préparer la requête SQL d'insertion
        $sql = "INSERT INTO fiches_inscription (nom, prenom, date_de_naissance, sexe, nationalite, email, telephone, mot_de_passe, dernier_diplome_obtenue, etablisement_precedent, formation_demande, specialisation, nom_contact_urgence, relation_etudiants, telephone_contact, email_contact)
        VALUES (:nom, :prenom, :date_de_naissance, :sexe, :nationalite, :email, :telephone, :mot_de_passe, :dernier_diplome_obtenue, :etablisement_precedent, :formation_demande, :specialisation, :nom_contact_urgence, :relation_etudiants, :telephone_contact, :email_contact)";

        // Préparer la déclaration
        $stmt = $database->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_de_naissance', $date_de_naissance);
        $stmt->bindParam(':sexe', $sexe);
        $stmt->bindParam(':nationalite', $nationalite);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':dernier_diplome_obtenue', $dernier_diplome_obtenue);
        $stmt->bindParam(':etablisement_precedent', $etablisement_precedent);
        $stmt->bindParam(':formation_demande', $formation_demande);
        $stmt->bindParam(':specialisation', $specialisation);
        $stmt->bindParam(':nom_contact_urgence', $nom_contact_urgence);
        $stmt->bindParam(':relation_etudiants', $relation_etudiants);
        $stmt->bindParam(':telephone_contact', $telephone_contact);
        $stmt->bindParam(':email_contact', $email_contact);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "<div style='color: green; font-weight: bold;'>Inscription réussie! Vos informations ont été enregistrées avec succès.</div>";
        } else {
            echo "<div style='color: red; font-weight: bold;'>Erreur lors de l'enregistrement. Veuillez réessayer plus tard.</div>";
        }
    }

} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>Erreur de connexion à la base de données: " . $e->getMessage() . "</div>";
}
?>

