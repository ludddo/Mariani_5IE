<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Docs;

function getClient() {
    $client = new Client();
    $client->setApplicationName('Google Docs API PHP Quickstart');
    $client->setScopes([
        Google\Service\Docs::DOCUMENTS,
        'https://www.googleapis.com/auth/drive'
    ]);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri('http://localhost/xampp/5IE/MMC/ALL3/oauth2callback.php');

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
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit();
        }
    }
    return $client;
}

    $jsonData = file_get_contents('risposte.json');
    $responses = json_decode($jsonData, true);


    $client = getClient();
    $service = new Docs($client);

    // ID del documento Google Docs originale
    $originalDocumentId = '1R12g4aVkWeBBHpZZhxgRe_BSTK9vwJ-23wsd0GGGaSU';

    // Crea una copia del documento originale
    $copyTitle = 'Copia del documento originale';
    $driveService = new Google\Service\Drive($client);
    $copy = new Google\Service\Drive\DriveFile(['name' => $copyTitle]);
    $newDocument = $driveService->files->copy($originalDocumentId, $copy);
    $newDocumentId = $newDocument->id;

    // Recupera il nuovo documento
    $document = $service->documents->get($newDocumentId);
    $content = $document->getBody()->getContent();

    $requests = [];
    $tableCounter = 0; // Contatore per le tabelle
    $dataAvailable = true; // Flag per verificare se ci sono ancora dati disponibili
    $lastProcessedIndex = 0; // Indice dell'ultimo elemento processato

    // Itera attraverso gli elementi del documento
    foreach ($content as $element) {
        if (!$dataAvailable) {
            break;
        }

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
                        if ($response === false) {
                            $dataAvailable = false;
                            $lastProcessedIndex = $element->endIndex;
                            break;
                        }
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

    // Gestisci la quinta tabella con i dati di dynamicData
    $tableCounter = 0; // Resetta il contatore per iterare di nuovo
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
    if ($endIndex > 0 && isset($responses['dynamicData'])) {
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

    // Se non ci sono più dati, cancella il resto del documento
    if (!$dataAvailable) {
        $lastElement = end($content);
        $lastEndIndex = $lastElement->endIndex - 1; // Escludi il carattere di nuova riga finale
        $requests[] = [
            'deleteContentRange' => [
                'range' => [
                    'startIndex' => $lastProcessedIndex,
                    'endIndex' => $lastEndIndex
                ]
            ]
        ];
    }

    // Esegui tutte le richieste in un'unica chiamata batchUpdate
    if (!empty($requests)) {
        $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $response = $service->documents->batchUpdate($newDocumentId, $batchUpdateRequest);

        echo json_encode(['success' => true, 'documentId' => $newDocumentId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna modifica da applicare.']);
    }
?>