<?php
class Azienda {
    public $nome;
    public $contatto;

    public function __construct($nome, $contatto) {
        $this->nome = $nome;
        $this->contatto = $contatto;
    }

    public function inserisciOfferta($offerta) {
        echo "<div class='alert alert-success'>Offerta inserita dall'azienda {$this->nome}</div>";
    }

    public function notificaRifiuto($motivo) {
        echo "<div class='alert alert-danger'>L'azienda {$this->nome} è stata notificata: $motivo</div>";
    }

    public function assegnaStudente($studente, $tirocinio) {
        $tirocinio->setStudente($studente);
        echo "<div class='alert alert-info'>L'azienda {$this->nome} ha assegnato lo studente {$studente->getNome()} al tirocinio.</div>";
    }
}

// Classe Responsabile
class Responsabile {
    private $nome;
    private $idResponsabile;

    public function __construct($nome, $idResponsabile) {
        $this->nome = $nome;
        $this->idResponsabile = $idResponsabile;
    }

    public function valutaOfferta($offerta) {
        if (rand(0, 1)) {
            $offerta->aggiornaStato("Approvata");
            echo "<div class='alert alert-success'>Offerta {$offerta->getId()} approvata.</div>";
        } else {
            $offerta->aggiornaStato("Rifiutata");
            echo "<div class='alert alert-danger'>Offerta {$offerta->getId()} rifiutata.</div>";
        }
    }

    public function approvaAccoppiamento($tirocinio) {
        $tirocinio->setStato("Approvato");
        echo "<div class='alert alert-success'>Tirocinio {$tirocinio->getId()} approvato.</div>";
    }

    public function rifiutaAccoppiamento($tirocinio) {
        $tirocinio->setStato("Rifiutato");
        echo "<div class='alert alert-danger'>Tirocinio {$tirocinio->getId()} rifiutato.</div>";
    }
}

// Classe Studente
class Studente {
    private $matricola;
    private $nome;
    private $email;

    public function __construct($matricola, $nome, $email) {
        $this->matricola = $matricola;
        $this->nome = $nome;
        $this->email = $email;
    }

    public function visualizzaOfferte($offerte) {
        foreach ($offerte as $offerta) {
            echo "<div class='alert alert-info'>Nuova offerta disponibile: {$offerta->getDescrizione()}</div>";
        }
    }

    public function accordoConAzienda($offerta) {
        echo "<div class='alert alert-success'>Accordo con l'azienda per l'offerta {$offerta->getId()}.</div>";
    }

    public function getNome() {
        return $this->nome;
    }
}

// Classe Offerta
class Offerta {
    private $idOfferta;
    private $descrizione;
    private $stato;

    public function __construct($idOfferta, $descrizione) {
        $this->idOfferta = $idOfferta;
        $this->descrizione = $descrizione;
        $this->stato = "In attesa";
    }

    public function aggiornaStato($stato) {
        $this->stato = $stato;
    }

    public function getId() {
        return $this->idOfferta;
    }

    public function getDescrizione() {
        return $this->descrizione;
    }

    public function getStato() {
        return $this->stato;
    }
}

// Classe Tirocinio
class Tirocinio {
    private $idTirocinio;
    private $studente;
    private $offerta;
    private $stato;

    public function __construct($idTirocinio, $offerta) {
        $this->idTirocinio = $idTirocinio;
        $this->offerta = $offerta;
        $this->stato = "In attesa";
    }

    public function setStudente($studente) {
        $this->studente = $studente;
    }

    public function setStato($stato) {
        $this->stato = $stato;
    }

    public function getStato() {
        return $this->stato;
    }

    public function getId() {
        return $this->idTirocinio;
    }

    public function stampaAccordo() {
        echo "<div class='alert alert-success'>Accordo di tirocinio stampato.</div>";
    }
}

// Classe Documento
class Documento {
    private $idDocumento;
    private $contenuto;

    public function __construct($idDocumento, $contenuto) {
        $this->idDocumento = $idDocumento;
        $this->contenuto = $contenuto;
    }

    public function stampa() {
        echo "<div class='alert alert-success'>Documento stampato.</div>";
    }
}

?>