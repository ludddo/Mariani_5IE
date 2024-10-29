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
    //$jsonData = file_get_contents('php://input');
    $jsonData = file_get_contents('risposte.json');
    $responses = json_decode($jsonData, true);

    $client = getClient();
    $service = new Docs($client);

    $newDocumentId = createDocumentCopy($client);
    $content = getDocumentContent($service, $newDocumentId);

    $requests = processTables($content, $responses);
    $requests = array_merge($requests, handleDynamicData($content, $responses));
    executeBatchUpdate($service, $newDocumentId, $requests);

    $requests = [];
    $document = $service->documents->get($newDocumentId);
    $content = $document->getBody()->getContent();
    $requests = array_merge($requests, handleLiftingIndex($content, $responses));
    executeBatchUpdate($service, $newDocumentId, $requests);

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

function processTables($content, $responses) {
    $requests = [];
    $tableCounter = 0;

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

                        if ($response === 'yes') {
                            $requests = array_merge($requests, updateTextStyle($yesCell, 'X'));
                        } elseif ($response === 'no') {
                            $requests = array_merge($requests, updateTextStyle($noCell, 'X'));
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
        foreach ($responses['dynamicData'] as $data) {
            $liftingIndex = $data['weight'] / $nioshScore;
            $liftingIndexText .= "Per l'oggetto " . $data['description'] . " l'indice di sollevamento è " . $liftingIndex . "\n";
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

        echo json_encode(['success' => true, 'documentId' => $documentId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna modifica da applicare.']);
    }
}


//RICORDATI DI CAMBIARE QUESTO

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest();
//} else {
//    echo json_encode(['error' => 'Accedi a questa pagina solo tramite richieste POST.']);
//}
?>