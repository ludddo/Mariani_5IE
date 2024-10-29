<?php
    //mysql_connect //deprecated
    //mysqli_connect //nuova versione ma stile non a oggetti
    
    $hostname = "localhost"; //127.0.0.1
    $username = "root";
    $password = "";
    $dbname = "musica";
    $port = 3306;
    $mysqli = new mysqli($hostname, $username, $password, $dbname, $port);
    
    if($mysqli->connect_errno)
    {
        die("Connessione fallita: ".$mysqli->connect_error);
    }
    
    //possiamo interagire con il DBMS con il DB musica
    //die("Connessione riuscita");

    $artisti = $mysqli->query("SELECT * FROM `artisti`");
    $brani = $mysqli->query("SELECT * FROM `brani`");
    

    if ($artisti == false || $brani == false )
    {
        die("Errore nella query: ".$mysqli->error);
    }

    //foreach ($artisti as $artista)
    while($artista = $artisti->fetch_assoc())
    {
        echo "<h1 class='alert alert-danger'>".$artista["Nome"]." ".$artista["Cognome"]."</h1>";
        echo "<img src='./img/".$artista["Immagine"]."' style='width: 300px'>";
        $sql="SELECT * FROM `brani_artisti` WHERE `ID_ARTISTA` = ".$artista["ID"];
        echo $sql;
        $brani_artisti = $mysqli->query($sql); //Prof ha detto di ottimizzare
        
        while($brano_artista = $brani_artisti->fetch_assoc())
        {    
            if($brano_artista["ID_ARTISTA"] == $artista["ID"])
            {
                $brano = $brani->fetch_assoc();
                echo "<h3 class='alert alert-primary'> Titolo: ".$brano["Titolo"]."</h3>";
                echo "<h3 class='alert alert-primary'> Album: ".$brano["Album"]."</h3>";
                echo "<h3 class='alert alert-primary'> Durata: ".$brano["Durata"]."</h3>";
            }
        }
    }
        /*$artista = $artisti->fetch_assoc();

        ?>
        <h1 class="alert alert-danger">
        <?php
        echo $artista["Nome"]." ".$artista["Cognome"];
        ?>
        </h1>
        <img src="./img/<?php echo $artista["Immagine"] ?>" style="width: 300px">
        <?php    
    }*/
    





?>