<?php
function rc4($key, $data) {
    $key = array_map('ord', str_split($key)); //convertono ogni carattere della stringa $key e $data nei rispettivi valori ASCII.
    $data = array_map('ord', str_split($data));
    
    $S = range(0, 255);
    $j = 0;
    
    //questa fase inizializza una permutazione del vettore S (di dimensione 256) utilizzando una chiave segreta di lunghezza arbitraria.
    //Ksa Key Scheduling Algorithm
    for ($i = 0; $i < 256; $i++) {
        $j = ($j + $S[$i] + $key[$i % count($key)]) % 256; //questa riga calcola un nuovo valore per l'indice $j che viene poi utilizzato per scambiare elementi nel vettore S
        // Scambia S[i] e S[j]
        $temp = $S[$i];
        $S[$i] = $S[$j];
        $S[$j] = $temp;
    }

    //questa fase genera il keystream, ovvero una sequenza pseudo-casuale di byte basata sul vettore S.
    //Prga Pseudo-Random Generation Algorithm
    $i = $j = 0;
    $keystream = [];
    $cipher = "";
    
    //il ciclo foreach itera su ogni byte del testo da cifrare, genera un byte del keystream, esegue l'operazione XOR per cifrare il byte e costruisce la stringa cifrata
    foreach ($data as $byte) {
        $i = ($i + 1) % 256;
        $j = ($j + $S[$i]) % 256;
        
        $temp = $S[$i];
        $S[$i] = $S[$j];
        $S[$j] = $temp;
        
        // Genera keystream e XOR con il byte del dato
        $keystreamByte = $S[($S[$i] + $S[$j]) % 256];
        $cipher .= chr($byte ^ $keystreamByte);
    }
    
    return $cipher;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $key = $_POST['key'];
    $data = $_POST['data'];
    $outputMode = $_POST['output-mode'];

    $ciphertext = rc4($key, $data);
    $plaintext = rc4($key, $ciphertext);

    if ($outputMode === 'file') {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="rc4_output.txt"');
        header('Content-Length: ' . strlen($ciphertext));
        echo "Key: " . $key . "\n";
        echo "Original Data: " . $data . "\n";
        echo "Encrypted Data: " . bin2hex($ciphertext) . "\n";
        echo "Decrypted Data: " . $plaintext;
        exit;
    } else {
        echo "<div style='padding: 20px; max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
        echo "<div style='padding: 20px; max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
        echo "<h1>RC4 Encryption</h1>";
        echo "<p><strong>Key:</strong> " . htmlspecialchars($key) . "</p>";
        echo "<p><strong>Original Data:</strong> " . htmlspecialchars($data) . "</p>";
        echo "<p><strong>Encrypted Data:</strong> " . bin2hex($ciphertext) . "</p>";
        echo "<p><strong>Decrypted Data:</strong> " . htmlspecialchars($plaintext) . "</p>";
        echo "<a href='index.html' style='display: block; text-align: center; margin-top: 20px;'>Back</a>";
        echo "</div>";
    }
   
}
?>
