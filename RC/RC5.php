<?php
function rc5Cifra($chiave, $testo, $cicli = 12, $lunghezzaParola = 32) {
    $modulo = 1 << $lunghezzaParola; // Modulo (2^lunghezzaParola)
    $maschera = $modulo - 1; // Maschera per operazioni modulo

    // Costanti magiche per RC5 (valori predeterminati)
    $P = 0xB7E15163; 
    $Q = 0x9E3779B9; 

    // Preparazione della chiave
    $paroleChiave = [];
    for ($i = 0; $i < strlen($chiave); $i += 4) {
        //questa riga divide la chiave in blocchi di 4 byte, li converte in interi non firmati a 32 bit e li memorizza
        $paroleChiave[] = unpack('V', substr($chiave . "\0\0\0\0", $i, 4))[1]; //'V' specifica che il valore deve essere interpretato come un intero non firmato a 32 bit
    }

    $totaleSottochiavi = 2 * ($cicli + 1); // Numero di sottochiavi
    $sottochiavi = [$P];
    for ($i = 1; $i < $totaleSottochiavi; $i++) {
        $sottochiavi[] = ($sottochiavi[$i - 1] + $Q) & $maschera;
    }

    // Mescolamento delle chiavi
    $indiceChiave = $indiceSottochiavi = $valoreA = $valoreB = 0;
    $lunghezzaChiave = count($paroleChiave);
    for ($k = 0; $k < 3 * max($totaleSottochiavi, $lunghezzaChiave); $k++) {
        $valoreA = $sottochiavi[$indiceSottochiavi] = rotazioneSinistra(($sottochiavi[$indiceSottochiavi] + $valoreA + $valoreB) & $maschera, 3, $lunghezzaParola);
        $valoreB = $paroleChiave[$indiceChiave] = rotazioneSinistra(($paroleChiave[$indiceChiave] + $valoreA + $valoreB) & $maschera, ($valoreA + $valoreB) % $lunghezzaParola, $lunghezzaParola);
        $indiceSottochiavi = ($indiceSottochiavi + 1) % $totaleSottochiavi;
        $indiceChiave = ($indiceChiave + 1) % $lunghezzaChiave;
    }

    //Arrotonda la lunghezza della stringa $testo al multiplo di 8 più vicino e riempie la stringa
    $testo = str_pad($testo, (strlen($testo) + 7) & ~7, "\0"); // complemento di 7
    $blocchi = str_split($testo, 8);
    $testoCifrato = "";

    foreach ($blocchi as $blocco) {
        [$valoreA, $valoreB] = array_values(unpack('V2', $blocco));

        // Fase di cifratura
        $valoreA = ($valoreA + $sottochiavi[0]) & $maschera;
        $valoreB = ($valoreB + $sottochiavi[1]) & $maschera;

        for ($i = 1; $i <= $cicli; $i++) {
            $valoreA = ($valoreA ^ $valoreB);
            $valoreA = rotazioneSinistra($valoreA, $valoreB % $lunghezzaParola, $lunghezzaParola);
            $valoreA = ($valoreA + $sottochiavi[2 * $i]) & $maschera;

            $valoreB = ($valoreB ^ $valoreA);
            $valoreB = rotazioneSinistra($valoreB, $valoreA % $lunghezzaParola, $lunghezzaParola);
            $valoreB = ($valoreB + $sottochiavi[2 * $i + 1]) & $maschera;
        }

        $testoCifrato .= pack('V2', $valoreA, $valoreB);
    }

    return $testoCifrato;
}

//STESSO CODICE DI CIFRATURA, MA CON LE OPERAZIONI IN ORDINE INVERSO
function rc5Decifra($chiave, $testoCifrato, $cicli = 12, $lunghezzaParola = 32) {
    $modulo = 1 << $lunghezzaParola;
    $maschera = $modulo - 1;

    // Costanti magiche per RC5
    $P = 0xB7E15163;
    $Q = 0x9E3779B9;

    // Preparazione della chiave
    $paroleChiave = [];
    for ($i = 0; $i < strlen($chiave); $i += 4) {
        $paroleChiave[] = unpack('V', substr($chiave . "\0\0\0\0", $i, 4))[1];
    }

    $totaleSottochiavi = 2 * ($cicli + 1);
    $sottochiavi = [$P];
    for ($i = 1; $i < $totaleSottochiavi; $i++) {
        $sottochiavi[] = ($sottochiavi[$i - 1] + $Q) & $maschera;
    }

    $indiceChiave = $indiceSottochiavi = $valoreA = $valoreB = 0;
    $lunghezzaChiave = count($paroleChiave);
    for ($k = 0; $k < 3 * max($totaleSottochiavi, $lunghezzaChiave); $k++) {
        $valoreA = $sottochiavi[$indiceSottochiavi] = rotazioneSinistra(($sottochiavi[$indiceSottochiavi] + $valoreA + $valoreB) & $maschera, 3, $lunghezzaParola);
        $valoreB = $paroleChiave[$indiceChiave] = rotazioneSinistra(($paroleChiave[$indiceChiave] + $valoreA + $valoreB) & $maschera, ($valoreA + $valoreB) % $lunghezzaParola, $lunghezzaParola);
        $indiceSottochiavi = ($indiceSottochiavi + 1) % $totaleSottochiavi;
        $indiceChiave = ($indiceChiave + 1) % $lunghezzaChiave;
    }

    $blocchi = str_split($testoCifrato, 8);
    $testoDecifrato = "";

    foreach ($blocchi as $blocco) {
        [$valoreA, $valoreB] = array_values(unpack('V2', $blocco));

        for ($i = $cicli; $i >= 1; $i--) {
            $valoreB = ($valoreB - $sottochiavi[2 * $i + 1]) & $maschera;
            $valoreB = rotazioneDestra($valoreB, $valoreA % $lunghezzaParola, $lunghezzaParola);
            $valoreB = ($valoreB ^ $valoreA);

            $valoreA = ($valoreA - $sottochiavi[2 * $i]) & $maschera;
            $valoreA = rotazioneDestra($valoreA, $valoreB % $lunghezzaParola, $lunghezzaParola);
            $valoreA = ($valoreA ^ $valoreB);
        }

        $valoreB = ($valoreB - $sottochiavi[1]) & $maschera;
        $valoreA = ($valoreA - $sottochiavi[0]) & $maschera;

        $testoDecifrato .= pack('V2', $valoreA, $valoreB);
    }

    return rtrim($testoDecifrato, "\0");
}

function rotazioneSinistra($valore, $spostamento, $lunghezzaParola) {
    return (($valore << $spostamento) | ($valore >> ($lunghezzaParola - $spostamento))) & ((1 << $lunghezzaParola) - 1);
}

function rotazioneDestra($valore, $spostamento, $lunghezzaParola) {
    return (($valore >> $spostamento) | ($valore << ($lunghezzaParola - $spostamento))) & ((1 << $lunghezzaParola) - 1);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $chiave = $_POST['key'];
    $testo = $_POST['data'];
    $outputMode = $_POST['output-mode'];

    $testoCifrato = rc5Cifra($chiave, $testo);
    $testoDecifrato = rc5Decifra($chiave, $testoCifrato);

    if ($outputMode === 'file') {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="rc5_output.txt"');
        header('Content-Length: ' . strlen($testoCifrato));
        echo "Key: " . $chiave . "\n";
        echo "Original Data: " . $testo . "\n";
        echo "Encrypted Data: " . bin2hex($testoCifrato) . "\n";
        echo "Decrypted Data: " . $testoDecifrato;
        exit;
    } else {
        echo "<div style='padding: 20px; max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
        echo "<div style='padding: 20px; max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
        echo "<h1>RC5 Encryption</h1>";
        echo "<p><strong>Key:</strong> " . htmlspecialchars($chiave) . "</p>";
        echo "<p><strong>Original Data:</strong> " . htmlspecialchars($testo) . "</p>";
        echo "<p><strong>Encrypted Data:</strong> " . bin2hex($testoCifrato) . "</p>";
        echo "<p><strong>Decrypted Data:</strong> " . htmlspecialchars($testoDecifrato) . "</p>";
        echo "<a href='index.html' style='display: block; text-align: center; margin-top: 20px;'>Back</a>";
        echo "</div>";
    }
    
}
?>
