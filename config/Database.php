<?php
class Database {
    private $host = "localhost";
    private $db_name = "refugio_pokemon"; // Asegúrate de que este es el nombre que usaste
    private $username = "root";
    private $password = ""; // En XAMPP suele estar vacío
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            // Configuramos para que nos avise de errores (excepciones)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Configuramos para que los resultados vengan como objetos
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            
            // Forzamos UTF-8 para evitar problemas con tildes y Ñs
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}