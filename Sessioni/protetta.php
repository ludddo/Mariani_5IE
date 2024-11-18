<?php

session_start();

echo ("<h1>Pagina Protetta</h1>");

if (isset($_SESSION["utente"])){
    if ($_SESSION["utente"] == "OK"){

        echo "Contatore Accessi: " . $_COOKIE["contaSuccess"];
        
    }
    else{

        echo "Non Autenticato";
    
    }
} else {
    echo "Non sei autenticato correttamente";
}

?>