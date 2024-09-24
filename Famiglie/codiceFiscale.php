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
        $famigliejson = file_get_contents("Famiglie.json");
        $db = json_decode($famigliejson, true);
        $codice_fiscale = $_GET["n"];
        $personaTrovata = null;

        $campi = [];
        $campi["id_famiglia"] = "Codice Famiglia";
        $campi["id_compo"] = "Componente numero";
        $campi["tipo"] = "Ruolo nella famiglia";
        $campi["cognome"] = "Cognome";
        $campi["nome"] = "Nome";
        $campi["sesso"] = "Sesso";
        $campi["nas_luogo"] = "Luogo di nascita";
        $campi["nas_regione"] = "Regione di nascita";
        $campi["nas_prov"] = "Provincia di nascita";
        $campi["nas_cap"] = "CAP del luogo di nascita";
        $campi["nas_belf"] = "Codice catastale del comune";
        $campi["nas_pre"] = "Prefisso telefonico";
        $campi["data_nascita"] = "Data di nascita";
        $campi["cod_fis"] = "Codice fiscale";
        $campi["res_luogo"] = "Luogo di residenza";
        $campi["res_regione"] = "Regione di residenza";
        $campi["res_prov"] = "Provincia di residenza";
        $campi["res_cap"] = "CAP del paese di residenza";
        $campi["indirizzo"] = "Indirizzo";
        $campi["telefono"] = "Numero di telefono";
        $campi["email"] = "E-mail";
        $campi["pwd_email"] = "Password";
        $campi["tit_studio"] = "Titolo di studio";
        $campi["professione"] = "Professione";
        $campi["sta_civ"] = "Stato civile";
        $campi["targa"] = "Targa dell'auto";
        $campi["part_iva"] = "Partita IVA";

        
        foreach ($db as $persona) {
            if ($persona['cod_fis'] === $codice_fiscale) {
                $personaTrovata = $persona;
                break;
            }
        }
    ?>
    <body>
        <div class="container mt-4">
            <h1 class="alert alert-info">Gestionale Famiglie</h1>

            <?php if ($personaTrovata): ?>
                <div class="card" style="width: 18rem;">
                    <img src="<?php echo $personaTrovata['sesso'] === 'F' ? 'img/donnaVivace.jpeg' : 'img/uomoVivace.jpeg'; ?>" class="card-img-top" alt="Immagine di <?php echo $personaTrovata['sesso'] === 'F' ? 'donna' : 'uomo'; ?>" />
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $personaTrovata['cognome'] . " " . $personaTrovata['nome']; ?></h5>
                        <p class="card-text">
                            <strong>Età:</strong> <?php echo $personaTrovata['data_nascita']; ?><br>
                            <strong>Indirizzo:</strong> <?php echo $personaTrovata['indirizzo']; ?><br>
                        </p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dettagliModal">
                            Mostra Dettagli
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger" role="alert">
                    Nessuna persona trovata con il codice fiscale fornito.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($personaTrovata): ?>
            <div class="modal fade" id="dettagliModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dettagliModalLabel">Dettagli di <?php echo $personaTrovata['cognome'] . " " . $personaTrovata['nome']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php foreach ($personaTrovata as $proprieta => $value): ?>
                                <strong><?php echo ucfirst($campi["$proprieta"]); ?>:</strong> <?php echo $value; ?><br>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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