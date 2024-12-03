<?php
// Configurazione database
require 'db.php';

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Determina la query da eseguire
if (isset($_GET['query'])) {
    $queryType = $_GET['query'];
    
    switch ($queryType) {
        case "1": // Interrogazioni di un alunno
            $alunno = $conn->real_escape_string($_GET['alunno']);
            $sql = "SELECT * FROM interrogazioni, alunni WHERE alunni.nome='$alunno' AND Id_alunno=alunni.id";
            break;
        
        case "2": // Interrogazioni di un alunno in una materia
            $alunno = $conn->real_escape_string($_GET['alunnoMateria']);
            $materia = $conn->real_escape_string($_GET['materia']);
            $sql = "SELECT * FROM interrogazioni, alunni, materie 
                    WHERE alunni.nome='$alunno' 
                    AND Id_alunno=alunni.id 
                    AND materie.nome='$materia' 
                    AND id_materia=materie.id";
            break;
        
        case "3": // Materie con interrogazioni
            $sql = "SELECT distinct materie.nome from interrogazioni,materie where id_materia=materie.id;";
            break;
        
        default:
            die("Query non valida.");
    }

    // Esecuzione della query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h1>Risultati</h1>";
        echo "<table border='1'><tr>";
        while ($fieldinfo = $result->fetch_field()) {
            echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
        }
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nessun risultato trovato.";
    }
}

$conn->close();
?>
