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
    
    require_once 'var.php';
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $_COOKIE["SQL"] = $conn;

    header("Location: protetta.php");
}
else{
    echo "Errore!!!";
    $_SESSION_UNSET;
    $_SESSION_DESTROY;
    header("Location: index.php");
}


?>