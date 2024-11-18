<!doctype html>
<html lang="it">
    <head>
        <title>Login</title>
    </head>
    <style>
        body{
            background-color: #333;
            color: aliceblue;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    <body>
        <?php
        if (isset($_COOKIE["blocco"])){
            echo "Hai superato il numero di tentativi consentiti<br>";
            echo "Numero di accessi sbagliati: " . $_COOKIE["conta"];
            exit();
        }
        ?>
        <h1>Pagina di Login</h1> 
        <form action="process.php">
            <label >Nome:</label>
            <input type="text" name="nome">
            <label >Password:</label>
            <input type="password" name="password">
            <input type="submit">
        </form>
    </body>
</html>