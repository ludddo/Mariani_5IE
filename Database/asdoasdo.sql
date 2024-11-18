-- Creazione della tabella FILM
CREATE TABLE Film (
    Codice INT PRIMARY KEY,
    Data_Uscita DATE,
    Titolo VARCHAR(255),
    Paese VARCHAR(50),
    Anno INT,
    Durata INT,
    Genere VARCHAR(50)
);

-- Creazione della tabella CAST
CREATE TABLE Cast (
    Codice INT PRIMARY KEY,
    Nome VARCHAR(255),
    Cognome VARCHAR(255),
    Ruolo VARCHAR(50),
    Data_Nascita DATE
);

-- Creazione della tabella PARTECIPAZIONE (Relazione tra FILM e CAST, N a 1 e 1 a N)
CREATE TABLE Partecipazione (
    Codice_Film INT,
    Codice_Cast INT,
    PRIMARY KEY (Codice_Film, Codice_Cast),
    FOREIGN KEY (Codice_Film) REFERENCES Film(Codice),
    FOREIGN KEY (Codice_Cast) REFERENCES `Cast`(Codice)
);

-- Creazione della tabella UTENTE
CREATE TABLE Utente (
    Username VARCHAR(50) PRIMARY KEY,
    Email VARCHAR(255),
    Password VARCHAR(255),
    Nome VARCHAR(255),
    Cognome VARCHAR(255)
);

-- Creazione della tabella PRENOTAZIONE
CREATE TABLE Prenotazione (
    ID INT PRIMARY KEY,
    Data DATE,
    Ora TIME,
    Pagamento DECIMAL(10, 2)
);

-- Creazione della tabella EFFETTUAZIONE (Relazione tra UTENTE e PRENOTAZIONE, 1 a 1)
CREATE TABLE Effettuazione (
    Username VARCHAR(50),
    ID_Prenotazione INT,
    PRIMARY KEY (Username, ID_Prenotazione),
    FOREIGN KEY (Username) REFERENCES Utente(Username),
    FOREIGN KEY (ID_Prenotazione) REFERENCES Prenotazione(ID)
);

-- Creazione della tabella PALINSESTO
CREATE TABLE Palinsesto (
    Codice INT PRIMARY KEY,
    Data DATE,
    Ora TIME
);

-- Creazione della tabella PROGRAMMAZIONE (Relazione tra PALINSESTO e FILM, 1 a N)
CREATE TABLE Programmazione (
    Codice_Palinsesto INT,
    Codice_Film INT,
    Prezzo_Biglietto DECIMAL(10, 2),
    Tipo VARCHAR(50),
    Valuta VARCHAR(10),
    PRIMARY KEY (Codice_Palinsesto, Codice_Film),
    FOREIGN KEY (Codice_Palinsesto) REFERENCES Palinsesto(Codice),
    FOREIGN KEY (Codice_Film) REFERENCES Film(Codice)
);

-- Creazione della tabella CINEMA
CREATE TABLE Cinema (
    ID INT PRIMARY KEY,
    Nome VARCHAR(255),
    Cantone VARCHAR(50),
    Recapito_Telefonico VARCHAR(15)
);

-- Creazione della tabella INDIRIZZO (Relazione con CINEMA, 1 a 1)
CREATE TABLE Indirizzo (
    ID_Cinema INT PRIMARY KEY,
    Via VARCHAR(255),
    CAP VARCHAR(10),
    Civico INT,
    Citta VARCHAR(50),
    FOREIGN KEY (ID_Cinema) REFERENCES Cinema(ID)
);

-- Creazione della tabella SALA
CREATE TABLE Sala (
    ID INT PRIMARY KEY,
    Numero INT
);

-- Creazione della tabella PLANIMETRIA (Relazione con SALA, 1 a 1)
CREATE TABLE Planimetria (
    ID_Sala INT PRIMARY KEY,
    Superficie DECIMAL(10, 2),
    Capienza INT,
    Numero_Posti INT,
    FOREIGN KEY (ID_Sala) REFERENCES Sala(ID)
);

-- Creazione della tabella DISPOSIZIONE (Relazione tra CINEMA e SALA, 1 a N)
CREATE TABLE Disposizione (
    ID_Cinema INT,
    ID_Sala INT,
    PRIMARY KEY (ID_Cinema, ID_Sala),
    FOREIGN KEY (ID_Cinema) REFERENCES Cinema(ID),
    FOREIGN KEY (ID_Sala) REFERENCES Sala(ID)
);

-- Creazione della tabella SELEZIONE (Relazione tra PALINSESTO e PRENOTAZIONE, 1 a 1)
CREATE TABLE Selezione (
    Codice_Palinsesto INT,
    ID_Prenotazione INT,
    PRIMARY KEY (Codice_Palinsesto, ID_Prenotazione),
    FOREIGN KEY (Codice_Palinsesto) REFERENCES Palinsesto(Codice),
    FOREIGN KEY (ID_Prenotazione) REFERENCES Prenotazione(ID)
);

-- Creazione della tabella GESTIONE (Relazione tra SALA e PRENOTAZIONE, 1 a N)
CREATE TABLE Gestione (
    ID_Sala INT,
    ID_Prenotazione INT,
    Posto INT,
    PRIMARY KEY (ID_Sala, ID_Prenotazione, Posto),
    FOREIGN KEY (ID_Sala) REFERENCES Sala(ID),
    FOREIGN KEY (ID_Prenotazione) REFERENCES Prenotazione(ID)
);
