<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'inscription-2';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Erreur de connexion à la base de données");
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Méthode sécurisée pour les requêtes préparées
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    // Méthode pour échapper les données (protection XSS)
    public static function escape($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    // Validation des entrées
    public static function validateInput($data, $type = 'string') {
        $data = trim($data);
        $data = stripslashes($data);
        
        switch($type) {
            case 'email':
                return filter_var($data, FILTER_VALIDATE_EMAIL) ? $data : false;
            case 'int':
                return filter_var($data, FILTER_VALIDATE_INT) ? $data : false;
            case 'string':
                return self::escape($data);
            default:
                return self::escape($data);
        }
    }
}
?>
