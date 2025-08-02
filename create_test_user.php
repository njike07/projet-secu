<?php
// Script pour créer un utilisateur de test
$host = 'localhost';
$dbname = 'inscription-2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si l'utilisateur test existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
    $stmt->execute(['test@cosendai.com']);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "✅ L'utilisateur de test existe déjà !<br>";
        echo "📧 Email: test@cosendai.com<br>";
        echo "🔑 Mot de passe: test123<br>";
    } else {
        // Créer l'utilisateur de test
        $hashedPassword = password_hash('test123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, date_creation) VALUES (?, ?, ?, ?, 'etudiant', NOW())");
        $result = $stmt->execute(['Test', 'Utilisateur', 'test@cosendai.com', $hashedPassword]);
        
        if ($result) {
            echo "✅ Utilisateur de test créé avec succès !<br>";
            echo "📧 Email: test@cosendai.com<br>";
            echo "🔑 Mot de passe: test123<br>";
        } else {
            echo "❌ Erreur lors de la création<br>";
        }
    }
    
    echo "<br><a href='simple_login.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Aller à la connexion</a>";
    
} catch (PDOException $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "<br>";
    echo "💡 Vérifiez que MySQL est démarré et que la base 'kamerhosting_amn' existe.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Création Utilisateur Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧪 Création Utilisateur de Test</h2>
        <hr>
        <?php // Le code PHP ci-dessus s'exécute ici ?>
    </div>
</body>
</html>
