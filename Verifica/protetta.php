<?php
session_start();

echo ("<h2>Pagina Protetta</h2>");

if (isset($_SESSION["utente"])) {
    if ($_SESSION["utente"] == "OK") {
        echo "Benvenuto ".$_SESSION['username'];
        echo "<br>Accesso effettuato correttamente tramite sessione";
    }
    else{
        echo "Non Autenticato";
    }   
} else if (isset($_COOKIE["autenticato"])) {
    if ($_COOKIE["autenticato"] == "SI") {
        echo "Benvenuto ".$_COOKIE['username'];
        echo "Accesso effettuato correttamente tramite cookie";
    }
    else{
        echo "Non Autenticato";
    }
} else {
    echo "Non sei autenticato correttamente e non puoi accedere a questa pagina";
    //header('Location: index.php');
}
?>