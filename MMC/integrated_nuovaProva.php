<?php

require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Docs;

function getClient() {
    $client = new Client();
    $client->setApplicationName('Google Docs API PHP');
    $client->setScopes([Docs::DOCUMENTS]);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Carica il token di accesso
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        echo "Token di accesso caricato con successo.\n";
    }

    // Richiedi un nuovo token se necessario
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            echo "Token aggiornato con il refresh token.\n";
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Apri il seguente link nel tuo browser:\n%s\n", $authUrl);
            print 'Inserisci il codice di verifica: ';
            $authCode = trim(fgets(STDIN));
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Salva il token sul file system
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            echo "Nuovo token generato e salvato.\n";
        }
    }
    return $client;
}

$client = getClient();
$service = new Docs($client);

// ID del documento Google Docs
$documentId = '1kJf9elB3zm18Dl1205-ACegkso-LXvQ8ynymRso5Yhs';

// Ottieni il contenuto del documento
try {
    $document = $service->documents->get($documentId);
    echo "Documento ottenuto con successo.\n";
} catch (Exception $e) {
    echo "Errore nel recupero del documento: " . $e->getMessage() . "\n";
    exit;
}

// Identifica la tabella e le celle da modificare
$tableIndex = 0;  // l'indice della tabella nel documento (0 per la prima tabella)
$rowIndex = 1;    // riga della cella da modificare
$cellIndex = 1;   // colonna della cella da modificare

// Funzione per ottenere l'indice della cella
function getTableCellIndex($document, $tableIndex, $rowIndex, $cellIndex) {
    $bodyContent = $document->getBody()->getContent();
    $tableCounter = 0;

    foreach ($bodyContent as $content) {
        if (isset($content->table)) {
            if ($tableCounter === $tableIndex) {
                $table = $content->table;
                $row = $table->getTableRows()[$rowIndex];
                $cell = $row->getTableCells()[$cellIndex];
                echo "Indice della cella trovato: StartIndex = " . $cell->getStartIndex() . "\n";
                return $cell->getStartIndex();
            }
            $tableCounter++;
        }
    }
    throw new Exception("Tabella o cella non trovata.");
}

// Trova l'indice iniziale della cella per inserire testo
try {
    $cellIndexStart = getTableCellIndex($document, $tableIndex, $rowIndex, $cellIndex);
} catch (Exception $e) {
    echo "Errore nell'individuazione dell'indice della cella: " . $e->getMessage() . "\n";
    exit;
}

// Inserisci un paragrafo vuoto se necessario, poi il testo desiderato
$requests = [
    new Google\Service\Docs\Request([
        'insertText' => [
            'location' => [
                'index' => $cellIndexStart,
            ],
            'text' => "\n" // Inserisce un paragrafo vuoto
        ]
    ]),
    new Google\Service\Docs\Request([
        'insertText' => [
            'location' => [
                'index' => $cellIndexStart + 1, // Posiziona dopo il paragrafo appena creato
            ],
            'text' => 'Nuovo contenuto'
        ]
    ])
];

// Esegui la richiesta di aggiornamento
try {
    $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
        'requests' => $requests
    ]);
    $response = $service->documents->batchUpdate($documentId, $batchUpdateRequest);
    echo "Aggiornamento eseguito con successo.\nRisposta API: ";
    var_dump($response);
} catch (Exception $e) {
    echo "Errore durante l'aggiornamento del documento: " . $e->getMessage() . "\n";
}
?>
