<?php
class Conexion {
    private $host = "localhost";
    private $db_name = "twilight"; // Nombre exacto de tu base de datos en phpMyAdmin
    private $username = "root";    // Usuario por defecto en XAMPP
    private $password = "";        // Contraseña por defecto (vacía)
    public $conn;

    public function getConexion() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>