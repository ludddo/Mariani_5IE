<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ./login.html");
    die();
}

echo "<h1>Welcome, " . htmlspecialchars($_SESSION["username"]) . "!</h1>";
echo "<h2>Your type is: " . htmlspecialchars($_SESSION["tipo"]) . "</h2>";

if ($_SESSION["tipo"] == "Admin") {
    echo "<h3>Here you can manage users:</h3>";
    $hostname = "localhost"; //
    $username = "root";
    $password = "";
    $dbname = "utenze";
    $port = 3306;
    $mysqli = new mysqli($hostname, $username, $password, $dbname, $port);

    if ($mysqli->connect_errno) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $utenti = $mysqli->query("SELECT * FROM `users`");

    if ($utenti == false) {
        die("Error in query: " . $mysqli->error);
    }

    foreach ($utenti as $utente) {

        if ($utente["Tipo"] != "Admin") {

            echo
                "<h4>Username: " . $utente["USERNAME"] . "</h4>" .
                "<h4>Password: " . $utente["Password"] . "</h4>" .
                "<h4>Type: " . $utente["Tipo"] . "</h4>";
        }    

        echo "<br>";
    }
}


echo '<form method="post" action="home.php">
    <button type="submit" name="logout">Logout</button>
</form>';

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ./login.html");
    exit();
}
?>
