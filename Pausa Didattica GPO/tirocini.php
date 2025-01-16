<?php
include 'class.php';

// Simulazione tirocini dello studente
$studente = new Studente("12345", "Luca Bianchi", "luca.bianchi@example.com");
$offerta1 = new Offerta(1, "Tirocinio sviluppo software");
$offerta2 = new Offerta(2, "Tirocinio marketing");

$tirocinio1 = new Tirocinio(1, $offerta1);
$tirocinio1->setStudente($studente);
$tirocinio1->setStato("In corso");

$tirocinio2 = new Tirocinio(2, $offerta2);
$tirocinio2->setStudente($studente);
$tirocinio2->setStato("In attesa");

$tirocini = [$tirocinio1, $tirocinio2];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Miei Tirocini - Sistema Gestione Tirocini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card { 
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .tirocinio-card {
            border-left: 4px solid #198754;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .stato-badge {
            float: right;
            padding: 5px 10px;
            border-radius: 15px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-briefcase-fill me-2"></i>
                Sistema Gestione Tirocini
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person"></i> Profilo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="bi bi-list-task"></i> Offerte</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="bi bi-journal-text"></i> I Miei Tirocini</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="aziende.php"><i class="bi bi-building"></i> Aziende</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-journal-text"></i> I Miei Tirocini</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($tirocini as $tirocinio): ?>
                            <div class="tirocinio-card">
                                <?php
                                $statoClass = match($tirocinio->getStato()) {
                                    "In corso" => "bg-success",
                                    "In attesa" => "bg-warning",
                                    "Completato" => "bg-info",
                                    default => "bg-secondary"
                                };
                                ?>
                                <span class="stato-badge badge <?php echo $statoClass; ?>">
                                    <?php echo $tirocinio->getStato(); ?>
                                </span>
                                <h5><?php echo $offerta1->getDescrizione(); ?></h5>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-text"></i> Documenti
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check-circle"></i> Conferma Presenza
                                    </button>
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-journal-plus"></i> Registro Attività
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Riepilogo</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tirocini Totali:</span>
                            <strong><?php echo count($tirocini); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>In Corso:</span>
                            <strong>1</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>In Attesa:</span>
                            <strong>1</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Completati:</span>
                            <strong>0</strong>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-calendar3"></i> Prossime Scadenze</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Consegna registro attività - 15/04/2024
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>