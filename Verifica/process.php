<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] == 'admin' && $_POST['password'] == 'admin') {
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['utente'] = 'OK';
        if (isset($_POST['remember'])) {
            setcookie('autenticato', 'SI', time() + 3600000);
            setcookie('username', $_POST['username'], time() + 3600000);
        }
        header('Location: protetta.php');
    } else {
        header('Location: index.php');
    }
} else {
    header('Location: index.php');
}
?>