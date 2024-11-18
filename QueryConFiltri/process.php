<?php
require "db.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM valutazioni WHERE 1=1";
    
    if (isset($_POST['voto']) && !empty($_POST['voto'])) {
        $sql .= " AND voto = :voto";
    }
    
    if (isset($_POST['data']) && !empty($_POST['data'])) {
        $sql .= " AND data = :data";
    }
    
    $statement = $conn->prepare($sql);
    
    if (isset($_POST['voto']) && !empty($_POST['voto'])) {
        $statement->bindParam(':voto', $_POST['voto'], PDO::PARAM_INT);
    }
    if (isset($_POST['data']) && !empty($_POST['data'])) {
        $statement->bindParam(':data', $_POST['data'], PDO::PARAM_STR);
    }
    
    $statement->execute();
    $data = $statement->fetchAll();
    
    if (count($data) > 0) {
        foreach ($data as $row) {
            echo "Id: " . $row[0] . " - Data: " . $row["data"] . " Voto: " . $row["voto"] . "<br>";
        }
    } else {
        echo "Nessun risultato trovato";
    }

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
} finally {
    $conn = null;
}
?>