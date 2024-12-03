<?php
    $mysqli = new mysqli("127.0.0.1", "ludovicomariani", "", "my_ludovicomariani");
    if ($mysqli->connect_errno) die("errore");

    $sql = "SELECT * FROM NEGOZIO_articolo ORDER BY Prezzo";
    $risultato = $mysqli->query($sql);

    if ($risultato) {
        while ($articolo = $risultato->fetch_assoc()) {
            echo "<h1>" . $articolo["Nome"] . "</h1>";
            $sql_immagini = "SELECT NEGOZIO_IMMAGINI.filename FROM NEGOZIO_IMMAGINI 
                             JOIN NEGOZIO_immagine_articolo ON NEGOZIO_IMMAGINI.ID = NEGOZIO_immagine_articolo.ID_IMMAGINE 
                             WHERE NEGOZIO_immagine_articolo.ID_ARTICOLO = " . $articolo["ID"];
            $immagini = $mysqli->query($sql_immagini);
            if ($immagini) {
                while ($imma = $immagini->fetch_assoc()) {
                    echo "<img src='./img/" . $imma["filename"] . "'/>";
                }
            } else {
                echo "Errore nella query delle immagini: " . $mysqli->error;
            }
        }
    } else {
        echo "Errore nella query degli articoli: " . $mysqli->error;
    }
?>