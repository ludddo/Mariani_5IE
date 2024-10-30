<?php

session_start();

if(!isset($_POST["username"]))
{
    header("Location: ./login.html");
    die();
}

$hostname = "localhost"; //127.0.0.1
$username = "root";
$password = "";
$dbname = "utenze";
$port = 3306;
$mysqli = new mysqli($hostname, $username, $password, $dbname, $port);

if($mysqli->connect_errno)
{
    die("Connessione fallita: ".$mysqli->connect_error);
}


$utenti = $mysqli->query("SELECT * FROM `users` WHERE `USERNAME` = '".$_POST["username"]."' AND `Password` = '".$_POST["password"]."'");


if ($utenti == false)
{
    die("Errore nella query: ".$mysqli->error);

} else if ($utenti->num_rows == 0)
{
    header("Location: ./login.html");
    die("Utente non trovato");

} else if ($utenti->num_rows == 1)
{
    $_SESSION["username"] = $_POST["username"];
    $_SESSION["tipo"] = $utenti->fetch_assoc()["Tipo"];
    header("Location: ./home.php");
    die();    
}

?>