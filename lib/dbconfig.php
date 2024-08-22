<?php

//namespace DB;
//use mysqli; //serve per funzioni query

/**
 * Classe per la gestione del database
 */
class Servizio { // Ho messo Servizio con la S maiuscola perche' mi urtava il sistema nervoso vedere tutto minuscolo
    private $connessione;
    private $host="localhost";
    private $user="root";
    private $psw="root";
    private $database="nmoretto";
    public $err_code;   // settato a true se presente errore, false se non ce errore
    public $err_text;   // setto lo stato dell'errore

    /**
     * Apre la connessione al database
     * @return mysqli|bool connessione
     */
    public function apriconn() {
        // assegna a $connessione l'oggetto che rappresenta la connessione
        $this->connessione = new mysqli($this->host, $this->user, $this->psw, $this->database);
        // se la connessione è fallita stampa un messaggio di errore
        if($this->connessione->connect_errno){
            $this->err_code=true;
            $this->err_text="Errore di connessione al db";
            return false;
        }else{
            // altrimenti conferma la connessione
            $this->err_code=false;
            //$this->err_text="Nessun errore";
            return $this->connessione;
        }
    }

    /**
     * Chiude la connessione al database
     * @return void
     */
    public function chiudiconn() {
        $this->connessione->close();
        //echo "connessione chiusa";
    }

    /**
     * Esegue una query sul database
     * @param string $statement query da eseguire
     * @param array $parameters parametri della query
     * @return mysqli_result|bool risultato della query o false se la query è fallita
     */
    public function query(string $statement,
        array $parameters = array()) {
        // prepara la query
        $stmt = $this->apriconn()->prepare($statement);

        // se la query è fallita stampa un messaggio di errore
        if ($stmt === false) {
            // throw new Exception("Cannot create statement for
            //     query: '{$statement}' error: '{$this->apriconn()->error}");
            $this->err_code=true;
            $this->err_text="Errore nella preparazione della richiesta";
            return false;
        }

        $format = "";
        $values = array();
        // associa i parametri della query
        foreach ($parameters as $valtype) {
            $format  .= $valtype[0];
            $values[] = $valtype[1];
        }

        // se la query è fallita stampa un messaggio di errore
        if ($format !== "") {
            $stmt->bind_param($format, ...$values);
        }
        // esegue la query
        $stmt->execute();
        // salva il risultato della query
        $result = $stmt->get_result();
        // chiude lo statement
        $stmt->close();
        // restituisce il risultato della query
        return $result;
    }

        // esegue la query
        /**
     * Funzione che gestisce il login
     * @param string $username username dell'utente
     * @param string $password password dell'utente
     * @return bool vero se l'utente esiste, falso altrimenti
     */
    public function login(string $username, string $password){
        // trasforma la password in sha256
        $hashed_password = hash('sha256', $password);
        // prepara la query
        $query = "SELECT * FROM user WHERE username = ? AND password = ?";
        $parameters = array("ss", $username, $hashed_password);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }
        $result = array();

        // associa i parametri della query
        $stmt->bind_param('ss', $username, $hashed_password);

        // esegue la query
        $stmt->execute();
        // salva il risultato della query
        $tmp = $stmt->get_result();
        $result = $tmp->fetch_assoc();

        // se il risulta è vuoto, l'utente non esiste
        if (empty($result)) {
            $this->err_code = true;
            $this->err_text = "Utente non trovato o password errata";
        } else {
            // altrimenti l'utente esiste
            $this->err_code = false;
        }

        // chiude lo statement
        $stmt->close();
        // ritorna il risultato
        return $result;
    }

    /**
     * Funzione che gestisce la registrazione
     * @param string $username username dell'utente
     * @param string $nome_cognome nome e cognome dell'utente
     * @param string $email email dell'utente
     * @param string $password password dell'utente
     * @param string $data_nascita data di nascita dell'utente
     * @param string $gender genere dell'utente
     * @return bool vero se l'utente è stato registrato, falso altrimenti
     */
    public function registrazione(string $username, string $nome_cognome, string $email, string $password, string $data_nascita, string $gender)
    {
        // trasforma la password in sha256
        $hashed_password = hash('sha256', $password);

        // prepara la query
        $query = "INSERT INTO user (username, name, email, password, birthdate, gender) VALUES (?, ?, ?, ?, ?, ?)";
        $parameters = array("ssssss", $username, $nome_cognome, $email, $hashed_password, $data_nascita, $gender);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);
        // esegue la query
        $stmt->execute();

        // controlla se la registrazione è avvenuta con successo
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la registrazione";
        }

        // chiude lo statement
        $stmt->close();

        // ritorna il risultato
        return !$this->err_code;
    }

    public function inserisci_post($testo, $utente){    //TODO: cambiare query inserendo anche i media nel db
        // prepara la query
        $query = "INSERT INTO post (content, username) VALUES (?, ?)";
        $parameters = array("ss", $testo, $utente);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);
        // esegue la query
        $stmt->execute();

        // controlla se l'inserimento è avvenuto con successo
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'inserimento del post";
        }

        // chiude lo statement
        $stmt->close();

        // ritorna il risultato
        return !$this->err_code;
    }

     /**
     * @brief Funzione che controlla se la password inserita è corretta
     * @param string $username username dell'utente
     * @param string $password password dell'utente
     * @return bool vero se la password inserita è corretta, falso altrimenti
     */
    public function check_password($username, $password): bool
    {
        // trasforma la password in sha256
        $hashed_password = hash('sha256', $password);
        // prepara la query
        $query = "SELECT * FROM user WHERE username = ? AND password = ?";
        $parameters = array("ss", $username, $hashed_password);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }
        $result = array();

        // associa i parametri della query
        $stmt->bind_param('ss', $username, $hashed_password);
        // esegue la query
        $stmt->execute();
        // salva il risultato della query
        $tmp = $stmt->get_result();
        $result = $tmp->fetch_assoc();
        $stmt->close();
        // se il risulta è vuoto, la password non è corretta
        if (empty($result)) {
            $this->err_code = true;
            $this->err_text = "Password errata";
            return false;
        } else {
            // altrimenti la password è corretta
            $this->err_code = false;
            return true;
        }
    }

    /**
     * Funzione che aggiorna la password corrente con una nuova
     * @param string $username username dell'utente
     * @param string $password password dell'utente
     * @return bool vero se la password è stata aggiornata, falso altrimenti
     */
    public function update_password($username, $password)
    {
        // trasforma la password in sha256
        $hashed_password = hash('sha256', $password);
        // prepara la query
        $query = "UPDATE user SET password = ? WHERE username = ?";
        $parameters = array("ss", $hashed_password, $username);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);
        // controlla che lo stmt sia stato eseguito correttamente
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }
        // esegue la query
        $stmt->execute();

        // controlla se l'aggiornamento è avvenuto con successo
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'aggiornamento della password";
        }

        // chiude lo statement
        $stmt->close();

        // ritorna il risultato
        return !$this->err_code;
    }

    public function modifica_dati_personali($username, $nome, $email, $bio, $gender, $birthdate, $location, $website){
        $conn = $this->apriconn();
        // prepara la query
        $query = "UPDATE user SET name = ?, email = ?, birthdate = ?, gender = ?, updated_at = NOW() WHERE username = ?";

        $query2 = "UPDATE profile SET bio = ?, location = ?, website = ?, updated_at = NOW() WHERE username = ?";
        $parameters = array("sssss", $nome, $email, $birthdate, $gender, $username);
        $parameters2 = array("ssss", $bio, $location, $website, $username);

        // prepara lo statement
        $stmt = $conn->prepare($query);
        $stmt2 = $conn->prepare($query2);
        if ($stmt === false || $stmt2 === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);
        $stmt2->bind_param(...$parameters2);
        // esegue la query
        $stmt->execute();
        $stmt2->execute();

        // controlla se la modifica è avvenuta con successo

        if ($stmt->affected_rows > 0 && $stmt2->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la modifica dei dati";
        }

        // chiude lo statement
        $stmt->close();
        $stmt2->close();

        // ritorna il risultato
        return !$this->err_code;
    }

    public function rimuovi_amicizia($username, $amico){
        // prepara la query
        $query = "DELETE FROM friendship WHERE (username_1 = ? AND username_2 = ?) OR (username_1 = ? AND username_2 = ?)";
        $parameters = array("ssss", $username, $amico, $amico, $username);
        // prepara lo statement
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);

        // esegue la query
        $stmt->execute();

        // controlla se la rimozione è avvenuta con successo
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la rimozione dell'amicizia";
        }
        
        // chiude lo statement
        $stmt->close();

        // ritorna il risultato
        return !$this->err_code;
    }

    public function datiutentemioprofilo($username){
        $conn = $this->apriconn();
        $query = "SELECT profile_picture_url, name, email, birthdate FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    public function eliminapost($id_post){
        $conn = $this->apriconn();
        $query = "DELETE FROM post WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione del post";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_like_count($id_post){
        $conn = $this->apriconn();
        $query = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data['count'];
    }
}