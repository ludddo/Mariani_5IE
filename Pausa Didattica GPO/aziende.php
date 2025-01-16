<?php
include 'class.php';

// Simulazione dati aziende
$aziende = [
    new Azienda("Tech Corp", "info@techcorp.com"),
    new Azienda("Marketing Pro", "contact@marketingpro.com"),
    new Azienda("Digital Solutions", "hr@digitalsolutions.com")
];

// Simulazione offerte per azienda
$offerte = [
    new Offerta(1, "Sviluppo Web Frontend"),
    new Offerta(2, "Digital Marketing"),
    new Offerta(3, "Backend Development")
];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aziende Partner - Sistema Gestione Tirocini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card { 
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .azienda-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
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
                <a class="nav-link" href="tirocini.php"><i class="bi bi-journal-text"></i> I Miei Tirocini</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="bi bi-building"></i> Aziende</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-building"></i> Aziende Partner</h5>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" placeholder="Cerca azienda...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($aziende as $index => $azienda): ?>
                            <div class="azienda-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5><?php echo $azienda->nome; ?></h5>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-envelope"></i> <?php echo $azienda->contatto; ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-primary">
                                        <?php echo rand(1, 5); ?> Tirocini attivi
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-info-circle"></i> Dettagli
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-chat"></i> Contatta
                                    </button>
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-list-task"></i> Vedi Offerte
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
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtri</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Settore</label>
                            <select class="form-select">
                                <option>Tutti i settori</option>
                                <option>IT</option>
                                <option>Marketing</option>
                                <option>Design</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Località</label>
                            <select class="form-select">
                                <option>Tutte le località</option>
                                <option>Milano</option>
                                <option>Roma</option>
                                <option>Torino</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Applica Filtri
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Statistiche</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Aziende Partner:</span>
                            <strong><?php echo count($aziende); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tirocini Attivi:</span>
                            <strong><?php echo rand(10, 20); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Settori Coperti:</span>
                            <strong>5</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>