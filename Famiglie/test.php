<!doctype html>
<html lang="it">
    <head>
        <title>Gestionale Famiglie</title>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>
    <?php 
        $famigliejson=file_get_contents("Famiglie.json");
        $db=json_decode($famigliejson);
        $n=$_GET["n"];
        $primaPersona= $db[$n];
    ?>
    <body>
    <div class="container mt-4">
            <h1 class="alert alert-info">Gestionale Famiglie</h1>

            <div class="card" style="width: 18rem;">
                <img src=<?php if ($primaPersona->sesso == "F") echo "img/donnaVivace.jpeg"; else echo "img/uomoVivace.jpeg";
                        ?> class="card-img-top"/>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $primaPersona->cognome . " " . $primaPersona->nome; ?></h5>
                    <p class="card-text">
                        <strong>Età:</strong> <?php echo $primaPersona->data_nascita; ?><br>
                        <strong>Indirizzo:</strong> <?php echo $primaPersona->indirizzo; ?><br>
                    </p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dettagliModal">
                        Mostra Dettagli
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="dettagliModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dettagliModalLabel">Dettagli di <?php echo $primaPersona->cognome . " " . $primaPersona->nome; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($primaPersona as $proprieta => $value): ?>
                            <?php echo $proprieta ?>:<?php echo $value; ?><br>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>