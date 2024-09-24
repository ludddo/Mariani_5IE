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
    <body>
        <div class="container mt-4">
            <h1 class="alert alert-info">Gestionale Famiglie</h1>
            <form action="codiceFiscale.php" method="get">
                <div class="mb-3">
                    <label for="n" class="form-label">Inserisci il Codice Fiscale</label>
                    <input type="text" class="form-control" id="n" name="n"/>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
            <form action="codiceFamiglia.php" method="get">
                <div class="mb-3">
                    <label for="n" class="form-label">Inserisci il Codice Famiglia</label>
                    <input type="text" class="form-control" id="n" name="n"/>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
            <form action="eta.php" method="get">
                <div class="mb-3">
                    <label for="n" class="form-label">Inserisci l'Età</label>
                    <input type="text" class="form-control" id="n" name="n"/>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
            <form action="provincia.php" method="get">
                <div class="mb-3">
                    <label for="provincia" class="form-label">Inserisci la Provincia</label>
                    <select class="form-control" id="provincia" name="provincia">
                        <?php
                            $famigliejson = file_get_contents("Famiglie.json");
                            $db = json_decode($famigliejson, true);
                            $province = [];

                            foreach ($db as $persona) {
                                if (isset($persona['res_prov']) && !in_array($persona['res_prov'], $province)) {
                                    $province[] = $persona['res_prov'];
                                }
                            }

                            sort($province);

                            foreach ($province as $provincia) {
                                echo "<option value=\"$provincia\">$provincia</option>";
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Invia</button>
            </form>
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