<?php
// Toujours démarrer la session avant toute sortie ou utilisation de $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    // Facultatif : tu peux configurer les paramètres ici AVANT session_start()
    // ini_set('session.cookie_lifetime', 3600);
    // ini_set('session.gc_maxlifetime', 3600);
    session_start();
}

require_once 'database.php';

class OAuth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function generateState() {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;
        return $state;
    }

    public function validateState($state) {
        return isset($_SESSION['oauth2state']) && $state === $_SESSION['oauth2state'];
    }

    public function getOrCreateUser($email, $nom, $prenom) {
        $stmt = $this->db->prepare("SELECT * FROM compte WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user;
        } else {
            $mot_de_passe = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $type = 'revendeur'; // ou 'fournisseur' selon le contexte
            $stmt = $this->db->prepare("INSERT INTO compte (nom, prenom, email, mot_de_passe, type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $type]);

            return [
                'id' => $this->db->lastInsertId(),
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'type' => $type
            ];
        }
    }

    public function startOAuthSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['type'] = $user['type'];
    }
}
