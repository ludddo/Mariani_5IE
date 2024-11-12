<?php
    //mysql_connect //deprecated
    //mysqli_connect //nuova versione ma stile non a oggetti

    header("Content-type: application/json; charset=utf-8");
	
    $hostname = "localhost"; //127.0.0.1
    $username = "ludovicomariani"; //username di Altervista
    $password = "";
    $dbname = "my_ludovicomariani"; // my_ + username di Altervista
    $mysqli = new mysqli($hostname, $username, $password, $dbname);
    
    if($mysqli->connect_errno)
    {
        http_response_code(500);
        $risp = new stdClass();
        $risp->errorCode = 500;
        $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
        die(json_encode($risp));
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    $richiesta = explode("/", $_SERVER["REQUEST_URI"]);
    
    // GET /artista/img/1
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && $richiesta[4] == "img") && $method == "GET")
    {   
    	$id = $richiesta[5];
        //risolvere con prepare
        $imma = $mysqli->query("SELECT Immagine FROM `MUSICA_artisti` WHERE ID=".$id);
        $immagine = $imma->fetch_assoc()["Immagine"];


        header("Content-type: image/jpeg");
        die(file_get_contents("img/".$immagine));
    }
    
    // GET /artista/id (GET /artista?id=1)
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && is_numeric($richiesta[4])) && !isset($richiesta[5]) && $method == "GET")
    {
        $id = $richiesta[4];
        $artista = $mysqli->query("SELECT * FROM `MUSICA_artisti` WHERE ID=".$id);
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        
        if ($artista == false)
        {
            http_response_code(404);
            $risp = new stdClass();
            $risp->errorCode = 404;
            $risp->errorMessage = "Artista non trovato";
            die(json_encode($risp));
        }
        
        $art = $artista->fetch_assoc();
        $risp = new stdClass();
        $risp->id = $art["ID"];
        $risp->nominativo = $art["Nome"]." ".$art["Cognome"];
        $risp->immagine = "http://localhost/Musica/artista/img/".$art["ID"];
        
        http_response_code(200);
        die(json_encode($risp));
    }

    // GET /artista
    if ((strtolower($richiesta[3]) == "artista") && !isset($richiesta[5]) && !isset($richiesta[4]) && $method == "GET")
    {
      $artisti = $mysqli->query("SELECT * FROM `MUSICA_artisti`");
      if($mysqli->connect_errno)
      {   
          //gestire json
          http_response_code(500);
          die("Connessione fallita: ".$mysqli->connect_error);
      }

      if ($artisti == false)
      {
          //gestire json
          die("Errore nella query: ".$mysqli->error);
      }

      $risp = array();
      //foreach ($artisti as $artista)
      while($artista = $artisti->fetch_assoc())
      {
          $art = new stdClass();
          $art->id=$artista["ID"];
          $art->nominativo=$artista["Nome"]." ".$artista["Cognome"];
          $risp[]=$art;
      }

      http_response_code(200);
      die(json_encode($risp));
	}

    // POST /artista
    if ((strtolower($richiesta[3]) == "artista") && $method == "POST")
    {
        $body = json_decode(file_get_contents("php://input"));
        $nome = $body->nome;
        $cognome = $body->cognome;
        $immagine = $body->immagine;
        $query = "INSERT INTO `MUSICA_artisti` (Nome, Cognome, Immagine) VALUES ('$nome', '$cognome', '$immagine')";
        $mysqli->query($query);
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        http_response_code(201);
        die(json_encode(["message" => "Artista inserito correttamente"]));
    }

    // PUT /artista/id
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && is_numeric($richiesta[4])) && $method == "PUT")
    {
        $id = $richiesta[4];
        $body = json_decode(file_get_contents("php://input"));
        $nome = $body->nome;
        $cognome = $body->cognome;
        $immagine = $body->immagine;
        $query = "UPDATE `MUSICA_artisti` SET Nome='$nome', Cognome='$cognome', Immagine='$immagine' WHERE ID=$id";
        $mysqli->query($query);
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        http_response_code(200);
        die(json_encode(["message" => "Artista modificato correttamente"]));
    }

    // DELETE /artista/id
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && is_numeric($richiesta[4])) && $method == "DELETE")
    {
        $id = $richiesta[4];
        $query = "DELETE FROM `MUSICA_artisti` WHERE ID=$id";
        $mysqli->query($query);
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        http_response_code(200);
        die(json_encode(["message" => "Artista eliminato correttamente"]));
    }

    // GET /artista/id/brani
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && is_numeric($richiesta[4]) && isset($richiesta[5]) && $richiesta[5] == "brani") && $method == "GET")
    {
        $id = $richiesta[4];
        
        // Prima query per ottenere gli ID dei brani associati all'artista
        $query = "SELECT ID_BRANO FROM `MUSICA_brani_artisti` WHERE ID_ARTISTA = $id";
        $result = $mysqli->query($query);
        
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        
        if ($result == false || $result->num_rows == 0)
        {
            http_response_code(404);
            $risp = new stdClass();
            $risp->errorCode = 404;
            $risp->errorMessage = "Brani non trovati";
            die(json_encode($risp));
        }
        
        $brani_ids = array();
        while($row = $result->fetch_assoc())
        {
            $brani_ids[] = $row["ID_BRANO"];
        }
        
        // Seconda query per ottenere i dettagli dei brani
        $brani_ids_str = implode(",", $brani_ids);
        $query = "SELECT ID, Titolo, Album, Durata FROM `MUSICA_brani` WHERE ID IN ($brani_ids_str)";
        $brani = $mysqli->query($query);
        
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        
        $risp = array();
        while($brano = $brani->fetch_assoc())
        {
            $br = new stdClass();
            $br->id = $brano["ID"];
            $br->titolo = $brano["Titolo"];
            $br->album = $brano["Album"];
            $br->durata = $brano["Durata"];
            $risp[] = $br;
        }
        
        http_response_code(200);
        die(json_encode($risp));
    }

   // GET /brano/{id} //restituisce il file mp3
    if ((strtolower($richiesta[3]) == "brano" && isset($richiesta[4]) && is_numeric($richiesta[4])) && !isset($richiesta[5]) && $method == "GET")
    {
        $id = $richiesta[4];
        $brano = $mysqli->query("SELECT Mp3 FROM `MUSICA_brani` WHERE ID=".$id);
        if($mysqli->connect_errno)
        {
            http_response_code(500);
            $risp = new stdClass();
            $risp->errorCode = 500;
            $risp->errorMessage = "Connessione fallita: ".$mysqli->connect_error;
            die(json_encode($risp));
        }
        
        if ($brano == false)
        {
            http_response_code(404);
            $risp = new stdClass();
            $risp->errorCode = 404;
            $risp->errorMessage = "Brano non trovato";
            die(json_encode($risp));
        }
        
        $file = $brano->fetch_assoc()["Mp3"];
        header("Content-type: audio/mpeg");
        die(file_get_contents("mp3/".$file));
    }
?>