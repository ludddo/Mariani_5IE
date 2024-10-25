<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Docs;

function getClient() {
    $client = new Client();
    $client->setApplicationName('Google Docs API PHP');
    $client->setScopes([Docs::DOCUMENTS, 'https://www.googleapis.com/auth/drive']);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri('http://localhost/5IE/oauth2callback.php');

    // Caricamento token di accesso se esistente
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // Richiedi autenticazione se il token non esiste o è scaduto
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Apri il seguente link nel browser:\n%s\n", $authUrl);
            print 'Inserisci il codice di verifica: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Salva il token sul file system
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
    }

    return $client;
}


$client = getClient();
$service = new Docs($client);

// ID del documento Google Docs
$documentId = '1R12g4aVkWeBBHpZZhxgRe_BSTK9vwJ-23wsd0GGGaSU';

// Esegui una ricerca per trovare tutte le occorrenze di "X"
$document = $service->documents->get($documentId);
$content = $document->getBody()->getContent();

$requests = [];

function processTextRun($textRun, &$requests) {
    if (isset($textRun->textRun) && strpos($textRun->textRun->content, 'X') !== false) {
        $startIndex = $textRun->startIndex;
        $endIndex = $textRun->endIndex;
        $requests[] = new Docs\Request([
            'updateTextStyle' => [
                'range' => [
                    'startIndex' => $startIndex,
                    'endIndex' => $endIndex
                ],
                'textStyle' => [
                    'foregroundColor' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 1.0,
                                'green' => 0.0,
                                'blue' => 0.0
                            ]
                        ]
                    ]
                ],
                'fields' => 'foregroundColor'
            ]
        ]);
    }
}

foreach ($content as $element) {
    if (isset($element->paragraph)) {
        foreach ($element->paragraph->elements as $textRun) {
            processTextRun($textRun, $requests);
        }
    } elseif (isset($element->table)) {
        foreach ($element->table->tableRows as $row) {
            foreach ($row->tableCells as $cell) {
                foreach ($cell->content as $cellElement) {
                    if (isset($cellElement->paragraph)) {
                        foreach ($cellElement->paragraph->elements as $textRun) {
                            processTextRun($textRun, $requests);
                        }
                    }
                }
            }
        }
    }
}

// Esegui le richieste sul documento
$batchUpdateRequest = new Docs\BatchUpdateDocumentRequest([
    'requests' => $requests
]);

$response = $service->documents->batchUpdate($documentId, $batchUpdateRequest);

echo "Tutte le 'X' di colore nero sono state sostituite con 'X' di colore rosso.";
?>