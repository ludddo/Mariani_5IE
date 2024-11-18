<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        form {
            margin: 0 auto;
            width: 50%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 10px;
            margin-top: 10%;
        }
        label {
            margin-top: 10px;
        }
        input {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION["utente"])) {
        if ($_SESSION["utente"] == "OK") {
            echo "Accesso effettuato correttamente tramite sessione";
            echo "Verrai reindirizzato alla pagina protetta";
            header('Location: protetta.php');
        }
    } else if (isset($_COOKIE["autenticato"])) {
        if ($_COOKIE["autenticato"] == "SI") {
            echo "Accesso effettuato correttamente tramite cookie";
            echo "Verrai reindirizzato alla pagina protetta";
            header('Location: protetta.php');
        }
    }
    ?>
    <form action="process.php" method="post">
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <br>
        <label for="remember">Vuoi essere ricordato?</label>
        <input type="checkbox" name="remember" id="remember">
        <br>
        <input type="submit" value="Login">
    </form>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>