<?php 

session_start();



if (isset($_GET["nome"]))
    $nome = $_GET["nome"];

if (isset($_GET["password"]))
    $password = $_GET["password"];


if (isset($_COOKIE["conta"])){
    $conta = $_COOKIE["conta"];
    $conta++;
    setcookie("conta", $conta, time() + 3600);
}
else{
    setcookie("conta", 1, time() + 3600);

}

if ($_COOKIE["conta"] > 1){
    echo "Hai superato il numero di tentativi consentiti";
    $_SESSION_UNSET;
    $_SESSION_DESTROY;
    setcookie("blocco", 1, time() + 3600);
    header("Location: index.php");
}


if ($nome == "Ludovico" && $password == "ludo") {
    echo "Sei Autenticato Correttamente";
    $_SESSION["utente"] = "OK";
    $conta = 0;
    setcookie("conta", $conta, time() - 1);
    $contaSuccess = $_COOKIE["contaSuccess"];
    $contaSuccess++;
    setcookie("contaSuccess", $contaSuccess, time() + 3600);
    header("Location: protetta.php");
}
else{
    echo "Errore!!!";
    $_SESSION_UNSET;
    $_SESSION_DESTROY;
    header("Location: index.php");
}


?>