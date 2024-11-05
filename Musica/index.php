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
        echo "sono nell'if di img"; 
    	$id = $richiesta[5];
        //risolvere con prepare
        $imma = $mysqli->query("SELECT Immagine FROM `MUSICA_artisti` WHERE ID=".$id);
        $immagine = $imma->fetch_assoc()["Immagine"];

        //gestire i vari errori

        header("Content-type: image/jpeg");
        die(file_get_contents("img/".$immagine));
    }
    
    // GET /artista/1 (GET /artista?id=1)
    if ((strtolower($richiesta[3]) == "artista" && isset($richiesta[4]) && is_numeric($richiesta[4])) && $method == "GET")
    {
        echo "sono nell'if di artista id";
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
    if ((strtolower($richiesta[3]) == "artista") && $method == "GET")
    {
        echo "sono nell'if di artista";
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
      /*
      [
          {
              "id": 1,
              "nominativo": "Vasco Rossi"
          },
          {
              "id": 2,
              "nominativo": "Elettra Lamborghini"
          }
      ]

      */
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


    //SE SEI BRAVO AGGIUNGI artista/brani CHE TI RITORNA TUTTI I BRANI DI UN ARTISTA
?>