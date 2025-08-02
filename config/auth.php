<?php
require_once 'database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Validation de mot de passe fort
    public function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
        }
        
        return empty($errors) ? true : $errors;
    }
    
    // Inscription sécurisée
    public function register($nom, $prenom, $email, $password, $type = 'etudiant') {
        $nom = Database::validateInput($nom);
        $prenom = Database::validateInput($prenom);
        $email = Database::validateInput($email, 'email');
        
        if (!$email) {
            return ['success' => false, 'message' => 'Email invalide'];
        }
        
        $passwordValidation = $this->validatePassword($password);
        if ($passwordValidation !== true) {
            return ['success' => false, 'message' => implode(', ', $passwordValidation)];
        }
        
        $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        try {
            $stmt = $this->db->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type, date_creation, statut) VALUES (?, ?, ?, ?, ?, NOW(), 'actif')");
            $stmt->execute([$nom, $prenom, $email, $hashedPassword, $type]);
            
            $this->logAction($this->db->getConnection()->lastInsertId(), 'inscription', 'Création de compte');
            
            return ['success' => true, 'message' => 'Inscription réussie'];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        }
    }
    
    // Connexion sécurisée
    public function login($email, $password, $remember = false) {
        $email = Database::validateInput($email, 'email');
        
        if (!$email) {
            $this->logConnectionAttempt($email, 'Email invalide', false);
            return ['success' => false, 'message' => 'Email invalide'];
        }
        
        if ($this->isBlocked($email)) {
            return ['success' => false, 'message' => 'Compte temporairement bloqué. Réessayez dans 15 minutes.'];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ? AND statut = 'actif'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                if (password_needs_rehash($user['mot_de_passe'], PASSWORD_ARGON2ID)) {
                    $newHash = password_hash($password, PASSWORD_ARGON2ID);
                    $updateStmt = $this->db->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                    $updateStmt->execute([$newHash, $user['id']]);
                }
                
                $this->startSecureSession($user, $remember);
                
                $this->logConnectionAttempt($email, 'Connexion réussie', true);
                $this->logAction($user['id'], 'connexion', 'Connexion réussie');
                
                return ['success' => true, 'message' => 'Connexion réussie', 'user' => $user];
            } else {
                $this->logConnectionAttempt($email, 'Mot de passe incorrect', false);
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur de connexion'];
        }
    }
    
    private function startSecureSession($user, $remember = false) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['login_time'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60);
            
            setcookie('remember_token', $token, $expiry, '/', '', true, true);
            
            $stmt = $this->db->prepare("UPDATE utilisateurs SET remember_token = ?, remember_expiry = FROM_UNIXTIME(?) WHERE id = ?");
            $stmt->execute([$token, $expiry, $user['id']]);
        }
    }
    
    public function checkPermission($requiredRole = null) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if ($requiredRole && $_SESSION['user_type'] !== $requiredRole && $_SESSION['user_type'] !== 'admin') {
            return false;
        }
        
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['login_time']);
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'admin';
    }
    
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logAction($_SESSION['user_id'], 'deconnexion', 'Déconnexion');
        }
        
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    // ✅ Correction ici : 'success' → 'succes'
    private function isBlocked($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as attempts FROM tentatives_connexion WHERE email = ? AND succes = 0 AND tentative_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result['attempts'] >= 5;
    }

    // ✅ Correction ici aussi : 'success' → 'succes'
    private function logConnectionAttempt($email, $details, $success) {
        $stmt = $this->db->prepare("INSERT INTO tentatives_connexion (email, ip_address, user_agent, details, succes, tentative_time) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $email,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            $details,
            $success ? 1 : 0
        ]);
    }
    
    private function logAction($userId, $action, $details) {
        $stmt = $this->db->prepare("INSERT INTO modifications (user_id, action, details, ip_address, user_agent, modification_time) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
