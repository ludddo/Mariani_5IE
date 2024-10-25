<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Docs;

// Funzione per ottenere il client di Google
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
    }

    // Richiedi un nuovo token se necessario
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
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
        }
    }
    return $client;
}

$client = getClient();
$service = new Docs($client);

// ID del documento Google Docs
$documentId = '1R12g4aVkWeBBHpZZhxgRe_BSTK9vwJ-23wsd0GGGaSU';

// Carica il file JSON
$jsonData = file_get_contents('risposte.json');
$responses = json_decode($jsonData, true);

// Recupera il documento
$document = $service->documents->get($documentId);
$content = $document->getBody()->getContent();

$requests = [];
$tableCounter = 0; // Contatore per le tabelle

// Itera attraverso gli elementi del documento
foreach ($content as $element) {
    if (isset($element->table)) {
        $tableCounter++;
        // Salta la prima tabella
        if ($tableCounter == 1) {
            continue;
        }

        // Gestisci le altre tabelle
        if ($tableCounter != 5) {
            foreach ($element->table->tableRows as $row) {
                // Controlla che ci siano almeno 3 celle nella riga
                if (count($row->tableCells) >= 3) {
                    $yesCell = $row->tableCells[1];
                    $noCell = $row->tableCells[2];

                    // Supponiamo che le risposte siano in ordine nel file JSON
                    $response = current($responses);
                    next($responses);

                    if ($response === 'yes') {
                        // Cambia il colore della "X" nella colonna SI
                        foreach ($yesCell->content as $cellElement) {
                            if (isset($cellElement->paragraph)) {
                                foreach ($cellElement->paragraph->elements as $textRun) {
                                    if (strpos($textRun->textRun->content, 'X') !== false) {
                                        $requests[] = [
                                            'updateTextStyle' => [
                                                'range' => [
                                                    'startIndex' => $textRun->startIndex,
                                                    'endIndex' => $textRun->endIndex
                                                ],
                                                'textStyle' => [
                                                    'foregroundColor' => [
                                                        'color' => [
                                                            'rgbColor' => [
                                                                'red' => 0.0,
                                                                'green' => 0.0,
                                                                'blue' => 0.0
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'fields' => 'foregroundColor'
                                            ]
                                        ];
                                    }
                                }
                            }
                        }
                    } elseif ($response === 'no') {
                        // Cambia il colore della "X" nella colonna NO
                        foreach ($noCell->content as $cellElement) {
                            if (isset($cellElement->paragraph)) {
                                foreach ($cellElement->paragraph->elements as $textRun) {
                                    if (strpos($textRun->textRun->content, 'X') !== false) {
                                        $requests[] = [
                                            'updateTextStyle' => [
                                                'range' => [
                                                    'startIndex' => $textRun->startIndex,
                                                    'endIndex' => $textRun->endIndex
                                                ],
                                                'textStyle' => [
                                                    'foregroundColor' => [
                                                        'color' => [
                                                            'rgbColor' => [
                                                                'red' => 0.0,
                                                                'green' => 0.0,
                                                                'blue' => 0.0
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'fields' => 'foregroundColor'
                                            ]
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// Esegui le richieste sul documento solo se ci sono richieste
if (!empty($requests)) {
    $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
        'requests' => $requests
    ]);

    $response = $service->documents->batchUpdate($documentId, $batchUpdateRequest);

    echo "Le modifiche sono state applicate con successo.";
} else {
    echo "Nessuna modifica da applicare.";
}

// Gestisci la quinta tabella con i dati di dynamicData
$tableCounter = 0; // Resetta il contatore per iterare di nuovo
$requests = []; // Resetta le richieste per la quinta tabella
$endIndex = 0; // Variabile per memorizzare l'indice di fine della quinta tabella
foreach ($content as $element) {
    if (isset($element->table)) {
        $tableCounter++;
        if ($tableCounter == 5) {
            $endIndex = $element->endIndex;
            break;
        }
    }
}

// Inserisci un paragrafo con i dati di dynamicData sotto la quinta tabella
if ($endIndex > 0) {
    $dynamicData = $responses['dynamicData'];
    $dynamicDataText = "";
    foreach ($dynamicData as $data) {
        $dynamicDataText .= "Descrizione: " . $data['description'] . "\n";
        $dynamicDataText .= "Numero di oggetti: " . $data['num_objects'] . "\n";
        $dynamicDataText .= "Numero di sollevamenti: " . $data['num_lifts'] . "\n";
        $dynamicDataText .= "Peso: " . $data['weight'] . " Kg\n";
        $dynamicDataText .= "Durata: " . $data['duration'] . " ore\n";
        $dynamicDataText .= "Numero di lavoratori: " . $data['workers'] . "\n";
        $dynamicDataText .= "\n";
    }

    $requests[] = [
        'insertText' => [
            'location' => [
                'index' => $endIndex
            ],
            'text' => $dynamicDataText
        ]
    ];
}

// Esegui le richieste sul documento solo se ci sono richieste
if (!empty($requests)) {
    $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
        'requests' => $requests
    ]);

    $response = $service->documents->batchUpdate($documentId, $batchUpdateRequest);

    echo "Le modifiche sono state applicate con successo.";
} else {
    echo "Nessuna modifica da applicare.";
}
?>