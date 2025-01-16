<?php
include "class.php";

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestione Tirocini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card { 
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .alert { margin-top: 10px; }
        .nav-link { color: #495057; }
        .nav-link.active { font-weight: bold; }
        .offerta-card {
            border-left: 4px solid #0d6efd;
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
            <a class="navbar-brand" href="#">
                <i class="bi bi-briefcase-fill me-2"></i>
                Sistema Gestione Tirocini
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door"></i> Home</a>
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
                <a class="nav-link active" href="#"><i class="bi bi-list-task"></i> Offerte</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tirocini.php"><i class="bi bi-journal-text"></i> I Miei Tirocini</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="aziende.php"><i class="bi bi-building"></i> Aziende</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-stars"></i> Offerte Disponibili</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $offerte = [
                            new Offerta(1, "Tirocinio sviluppo software"),
                            new Offerta(2, "Tirocinio marketing")
                        ];
                        
                        foreach ($offerte as $offerta) {
                            echo "<div class='offerta-card'>";
                            echo "<span class='stato-badge badge bg-info'>{$offerta->getStato()}</span>";
                            echo "<h5>{$offerta->getDescrizione()}</h5>";
                            echo "<div class='mt-3'>";
                            echo "<button class='btn btn-sm btn-outline-primary'><i class='bi bi-info-circle'></i> Dettagli</button> ";
                            echo "<button class='btn btn-sm btn-success'><i class='bi bi-check-circle'></i> Candidati</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Profilo Studente</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $studente = new Studente("12345", "Luca Bianchi", "luca.bianchi@example.com");
                        echo "<div class='text-center mb-3'>";
                        echo "<i class='bi bi-person-circle' style='font-size: 4rem;'></i>";
                        echo "</div>";
                        echo "<h5 class='text-center'>{$studente->getNome()}</h5>";
                        echo "<hr>";
                        echo "<p class='mb-1'><i class='bi bi-envelope'></i> Email: luca.bianchi@example.com</p>";
                        echo "<p class='mb-1'><i class='bi bi-card-text'></i> Matricola: 12345</p>";
                        ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bell"></i> Notifiche</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Nuova offerta di tirocinio disponibile
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>