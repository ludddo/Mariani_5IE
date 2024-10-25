<?php
require 'vendor/autoload.php';

use Google\Client;

// Crea una nuova istanza del client Google
$client = new Client();
$client->setApplicationName('Google Docs API PHP');
$client->setScopes([Google\Service\Docs::DOCUMENTS, 'https://www.googleapis.com/auth/drive']);
$client->setAuthConfig('credentials.json');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Imposta il redirect URI corretto (deve essere lo stesso usato nelle credenziali e nel client PHP)
$client->setRedirectUri('http://localhost/xampp/5IE/MMC/ALL3/oauth2callback.php');

// Controlla se l'URL ha un parametro "code" (dopo che l'utente ha autorizzato l'accesso)
if (!isset($_GET['code'])) {
    // Se non è presente il parametro "code", reindirizza l'utente alla pagina di autorizzazione
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} else {
    // Se è presente il parametro "code", ottieni il token di accesso usando il codice di autorizzazione
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
    }

    // Salva il token in un file per futuri utilizzi
    file_put_contents('token.json', json_encode($client->getAccessToken()));

    // Reindirizza alla pagina principale (o un'altra pagina a tua scelta)
    header('Location: http://localhost/xampp/5IE/MMC/ALL3/process.php');
    exit;
}
?>
