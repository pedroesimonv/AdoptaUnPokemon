<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // En local (XAMPP), cargamos el archivo si existe
        if (file_exists(__DIR__ . '/../.env')) {
            $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        }

        // Mapeo híbrido: Prioriza $_ENV (local) y recurre a getenv() (Railway) si da null
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? null;
        $this->port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '6543';
        $this->db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'postgres';
        $this->username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'postgres';
        $this->password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? null;
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";options='--application_name=" . $this->host . "'";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch(PDOException $exception) {
            echo "Error de conexión a la nube: " . $exception->getMessage();
        }
        return $this->conn;
    }
}