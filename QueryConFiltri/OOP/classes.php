<?php



class Query {
    private $sql;
    private $conn;
    private $statement;

    public function __construct() {
        require "../db.php";
        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->sql = "SELECT * FROM valutazioni WHERE 1=1";
        } catch(PDOException $e) {
            throw new Exception("Errore connessione: " . $e->getMessage());
        }
    }

    public function get_voto() {
        if (isset($_POST['voto']) && !empty($_POST['voto'])) {
            $this->sql .= " AND voto = :voto";
        }
        return $this;
    }

    public function get_data() {
        if (isset($_POST['data']) && !empty($_POST['data'])) {
            $this->sql .= " AND data = :data";
        }
        return $this;
    }

    public function esegui() {
        try {
            $this->statement = $this->conn->prepare($this->sql);
            
            if (isset($_POST['voto']) && !empty($_POST['voto'])) {
                $this->statement->bindParam(':voto', $_POST['voto'], PDO::PARAM_INT);
            }
            if (isset($_POST['data']) && !empty($_POST['data'])) {
                $this->statement->bindParam(':data', $_POST['data'], PDO::PARAM_STR);
            }
            
            $this->statement->execute();
            return $this->statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Errore query: " . $e->getMessage());
        }
    }

    public function __destruct() {
        $this->conn = null;
    }
}