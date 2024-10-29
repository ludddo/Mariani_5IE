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

function handlePostRequest() {
    $jsonData = file_get_contents('php://input');
    $responses = json_decode($jsonData, true);

    $client = getClient();
    $service = new Docs($client);

    $newDocumentId = createDocumentCopy($client);
    $content = getDocumentContent($service, $newDocumentId);

    $requests = processTables($content, $responses, $newDocumentId, $service);
    $requests = array_merge($requests, handleDynamicData($content, $responses));
    $ritornamento = executeBatchUpdate($service, $newDocumentId, $requests);

    $requests = [];
    $document = $service->documents->get($newDocumentId);
    $content = $document->getBody()->getContent();
    $requests = array_merge($requests, handleLiftingIndex($content, $responses));
    executeBatchUpdate($service, $newDocumentId, $requests);
    echo $ritornamento;
}

function createDocumentCopy($client) {
    $originalDocumentId = '1R12g4aVkWeBBHpZZhxgRe_BSTK9vwJ-23wsd0GGGaSU';
    $copyTitle = 'All. 3 - Valutazione rischi sollevamento manuale carichi';
    $driveService = new Google\Service\Drive($client);
    $copy = new Google\Service\Drive\DriveFile(['name' => $copyTitle]);
    $newDocument = $driveService->files->copy($originalDocumentId, $copy);
    return $newDocument->id;
}

function getDocumentContent($service, $documentId) {
    $document = $service->documents->get($documentId);
    return $document->getBody()->getContent();
}

function processTables($content, $responses, $documentId, $service) {
    $requests = [];
    $tableCounter = 0;
    $jsonAttributeCounter = 0;

    foreach ($content as $element) {
        if (isset($element->table)) {
            $tableCounter++;
            if ($tableCounter == 1) {
                continue;
            }

            if ($tableCounter != 5) {
                foreach ($element->table->tableRows as $row) {
                    if (count($row->tableCells) >= 3) {
                        $yesCell = $row->tableCells[1];
                        $noCell = $row->tableCells[2];

                        $response = current($responses);
                        next($responses);
                        $jsonAttributeCounter++;

                        if ($response === 'yes') {
                            $requests = array_merge($requests, updateTextStyle($yesCell, 'X'));
                        } elseif ($response === 'no') {
                            $requests = array_merge($requests, updateTextStyle($noCell, 'X'));
                        }

                        // Check if the next attribute is 'dynamicData'
                        if (($jsonAttributeCounter == 5 || $jsonAttributeCounter == 6 || ($jsonAttributeCounter == 7 && $response == 'yes') || $jsonAttributeCounter == 23 /*|| $jsonAttributeCounter == 30*/) && key($responses) === 'dynamicData') {
                            $startIndex = $element->endIndex;
                            executeBatchUpdate($service, $documentId, $requests);
                            deleteLinesFromGoogleDoc($documentId, $startIndex);
                            return $requests;
                        }
                    }
                }
            }
        }
    }

    return $requests;
}

function updateTextStyle($cell, $text) {
    $requests = [];
    foreach ($cell->content as $cellElement) {
        if (isset($cellElement->paragraph)) {
            foreach ($cellElement->paragraph->elements as $textRun) {
                if (strpos($textRun->textRun->content, $text) !== false) {
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
    return $requests;
}

function handleDynamicData($content, $responses) {
    $requests = [];
    $tableCounter = 0;
    $endIndex = 0;

    foreach ($content as $element) {
        if (isset($element->table)) {
            $tableCounter++;
            if ($tableCounter == 5) {
                $endIndex = $element->endIndex;
                break;
            }
        }
    }

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

    return $requests;
}

function handleLiftingIndex($content, $responses) {
    $requests = [];
    $tableCounter = 0;
    $endIndex = 0;

    foreach ($content as $element) {
        if (isset($element->table)) {
            $tableCounter++;
            if ($tableCounter == 9) {
                $endIndex = $element->endIndex;
                break;
            }
        }
    }

    if ($endIndex > 0) {
        $nioshScore = $responses['nioshScore'];
        $liftingIndexText = "";
        if ($nioshScore == 0) {
            $liftingIndexText .= "La situazione è accettabile e non è richiesto alcuno specifico intervento.\n\n";
        } else {
            foreach ($responses['dynamicData'] as $data) {
                $liftingIndex = $data['weight'] / $nioshScore;
                $liftingIndexText .= "Per l'oggetto " . $data['description'] . " l'indice di sollevamento è " . $liftingIndex . "\n";

                //AGGIUNGI A LIftingindextest se R < 0.85 la situazione é accettabile
                if ($liftingIndex < 0.85) {
                    $liftingIndexText .= "La situazione è accettabile e non è richiesto alcuno specifico intervento.\n\n";
                } else if ($liftingIndex >= 0.85 && $liftingIndex < 1) {
                    $liftingIndexText .= "La situazione si avvicina ai limiti; una quota della popolazione (a dubbia esposizione) può essere non protetta e pertanto occorrono cautele, anche se non è necessario un intervento immediato. E’ comunque consigliato attivare la formazione e, a discrezione del medico, la sorveglianza sanitaria del personale addetto.\n\n";
                } else {
                    $liftingIndexText .= "la situazione può comportare un rischio per quote crescenti di popolazione e pertanto richiede un intervento di prevenzione primaria. Il rischio è tanto più elevato quanto maggiore è l’indice. Vi è necessità di un intervento immediato di prevenzione per situazioni con indice maggiore di 3; l’intervento è comunque necessario anche con indici compresi tra 1,25 e 3. E’ utile programmare gli interventi identificando le priorità di rischio. Successivamente riverificare l’indice di rischio dopo ogni intervento. Va comunque attivata la sorveglianza sanitaria periodica del personale esposto con periodicità bilanciata in funzione del livello di rischio.\n\n";
                }
            }
        }

        $requests[] = [
            'insertText' => [
                'location' => [
                    'index' => $endIndex
                ],
                'text' => $liftingIndexText
            ]
        ];
    }

    return $requests;
}

function executeBatchUpdate($service, $documentId, $requests) {
    if (!empty($requests)) {
        $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $response = $service->documents->batchUpdate($documentId, $batchUpdateRequest);

        return json_encode(['success' => true, 'documentId' => $documentId]);
    } else {
        return json_encode(['success' => false, 'message' => 'Nessuna modifica da applicare.']);
    }
}

function deleteLinesFromGoogleDoc($documentId, $startIndex) {
    $client = getClient();
    $service = new Google\Service\Docs($client);

    // Ottieni il contenuto del documento
    $document = $service->documents->get($documentId);
    $content = $document->getBody()->getContent();
    $lastElement = end($content);
    $endIndex = $lastElement->getEndIndex() - 1;

    // Crea una richiesta di aggiornamento per rimuovere le righe specificate
    $requests = [
        new Google\Service\Docs\Request([
            'deleteContentRange' => [
                'range' => [
                    'startIndex' => $startIndex,
                    'endIndex' => $endIndex
                ]
            ]
        ])
    ];

    // Esegui la richiesta di aggiornamento
    $batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
        'requests' => $requests
    ]);
    $service->documents->batchUpdate($documentId, $batchUpdateRequest);
    
    echo json_encode(['success' => true, 'documentId' => $documentId]);
    exit();
}

//RICORDATI DI CAMBIARE QUESTO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest();
} else {
    echo json_encode(['error' => 'Accedi a questa pagina solo tramite richieste POST.']);
}
?>