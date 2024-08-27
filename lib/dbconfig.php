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

        $profile_picture_path = "media/profile_pictures/default.jpg";

        // prepara la query
        $query = "INSERT INTO user (username, name, email, password, birthdate, gender, profile_picture_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $parameters = array("sssssss", $username, $nome_cognome, $email, $hashed_password, $data_nascita, $gender, $profile_picture_path);
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

    /**
     * Funzione che gestisce l'inserimento di un post
     * @param $testo string testo del post
     * @param $utente string username dell'utente che ha scritto il post
     * @param $media_path string path del media allegato al post (opzionale)
     * @return bool vero se il post è stato inserito, falso altrimenti
     */
    public function inserisci_post($testo, $utente, $media_path = null){    //TODO: cambiare query inserendo anche i media nel db

        // Query per inserire il post
        $query = "INSERT INTO post (content, username) VALUES (?, ?)";
        $parameters = array("ss", $testo, $utente);
        $stmt = $this->apriconn()->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        $stmt->bind_param(...$parameters);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'inserimento del post";
        }

        $stmt->close();

        // Query per recuperare l'id dell'ultimo post inserito
        $query = "SELECT post_id FROM post WHERE username = ? ORDER BY created_at DESC LIMIT 1";
        $parameters = array("s", $utente);
        $stmt = $this->apriconn()->prepare($query);
        $stmt->bind_param(...$parameters);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $post_id = $data['post_id'];

        // Se $media_path è null, significa che non è stato inserito alcun media
        if($media_path != null) {
            // Query per inserire il media
            $query = "UPDATE post SET media_path = ? WHERE post_id = ?";
            $parameters = array("si", $media_path, $post_id);

            $stmt = $this->apriconn()->prepare($query);
            if ($stmt === false) {
                $this->err_code = true;
                $this->err_text = "Errore nella preparazione della richiesta";
                return false;
            }

            $stmt->bind_param(...$parameters);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $this->err_code = false;
            } else {
                $this->err_code = true;
                $this->err_text = "Errore durante l'inserimento del post";
            }

            $stmt->close();
        }

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


    /**
     * Funzione che gestisce la modifica dei dati personali dell'utente
     * @param $username string username dell'utente
     * @param $nome string nome dell'utente
     * @param $email string email dell'utente
     * @param $bio string bio dell'utente
     * @param $gender string genere dell'utente
     * @param $birthdate string data di nascita dell'utente
     * @param $location string luogo dell'utente
     * @param $website string sito web dell'utente
     * @return bool vero se i dati sono stati modificati, falso altrimenti
     */
    public function modifica_dati_personali($username, $nome, $email, $bio, $gender, $birthdate, $location, $website, $profile_picture_path = null){
        $conn = $this->apriconn();

        if($profile_picture_path != null) {
            $query = "UPDATE user SET name = ?, email = ?, birthdate = ?, gender = ?, profile_picture_path = ?, updated_at = NOW() WHERE username = ?";
            $parameters = array("ssssss", $nome, $email, $birthdate, $gender, $profile_picture_path, $username);
        } else {
            $query = "UPDATE user SET name = ?, email = ?, birthdate = ?, gender = ?, updated_at = NOW() WHERE username = ?";
            $parameters = array("sssss", $nome, $email, $birthdate, $gender, $username);
        }

        $query2 = "UPDATE profile SET bio = ?, location = ?, website = ?, updated_at = NOW() WHERE username = ?";
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

    /**
     * Funzione che gestisce la rimozione dell'amicizia tra due utenti
     * @param $username string username dell'utente
     * @param $amico string username dell'amico
     * @return bool vero se l'amicizia è stata rimossa, falso altrimenti
     */
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

    /**
     * Funzione che restituisce i dati riguardanti il profilo dell'utente
     * @param $username string username dell'utente
     * @return array|false|null dati dell'utente o false se la query è fallita
     */
    public function get_dati_utente_profilo($username){
        $conn = $this->apriconn();
        $query = "SELECT profile_picture_path, name, email, birthdate FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    /**
     * Funzione che gestisce l'eliminazione di un post
     * @param $id_post int id del post
     * @return bool vero se il post è stato eliminato, falso altrimenti
     */
    public function elimina_post($id_post){
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

    /**
     * Funzione che restituisce il numero di like di un post
     * @param $id_post int id del post
     * @return mixed dati del post
     */
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

    /**
     * Funzione che controlla se il media è supportato e rispetta i limiti
     * @param $type string tipo del media
     * @param $size int dimensione del media
     * @return string messaggio di successo o errore
     */
    public function check_media($type, $size){
        $allowed = array('image/jpeg', 'image/png', 'image/gif', 'video/mp4');
        if (!in_array($type, $allowed)) {
            return "Formato non supportato";
        }
        if ($size > MB * 20) {
            return "Dimensione massima consentita 20MB";
        }
        return "successo";
    }

    /**
     * Funzione che restituisce il path del media di un post
     * @param $id_post int id del post
     * @return mixed path del media
     */
    public function get_media_path($id_post){
        $conn = $this->apriconn();
        $query = "SELECT media_path FROM post WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data['media_path'];
    }

    /**
     * Funzione che restituisce il tipo del media di un post
     * @param $id_post int id del post
     * @return string tipo del media
     */
    public function get_media_type($id_post){
        $conn = $this->apriconn();
        $query = "SELECT media_path FROM post WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        if($data['media_path'] == null){
            return "none";
        }

        $content_type = mime_content_type($data['media_path']);


        if($content_type == "image/jpeg" || $content_type == "image/png" || $content_type == "image/gif"){
            return "image";
        } else if($content_type == "video/mp4"){
            return "video";
        }
        return "unknown";
    }

    /**
     * Funzione che filtra il nome del file con vari accorgimenti
     * @param $filename string nome del file
     * @param $beautify bool se true applica ulteriori filtri
     * @return string filename filtrato
     */
    public function filter_filename(string $filename, bool $beautify=true): string
    {
        // prima di tutto elimina i caratteri speciali
        $filename = preg_replace(
            '~
                    [<>:"/\\\|?*]|
                    [\x00-\x1F]|
                    [\x7F\xA0\xAD]|
                    [#\[\]@!$&\'()+,;=]|
                    [{}^\~`]
                    ~x',
            '-', $filename);

        // evitare "." o ".." e ".hiddenFiles"
        $filename = ltrim($filename, '.-');

        // alcune aggiunge opzionali
        if($beautify){
            $filename = $this->beautify_filename($filename);
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    }

    /**
     * Funzione che rende il nome del file più leggibile
     * @param string $filename nome del file
     * @return string nome del file formattato
     */
    private function beautify_filename(string $filename): string
    {
        // elimina caratteri consecutivi
        $filename = preg_replace(array('/ +/', '/_+/', '/-+/'), '-', $filename);
        $filename = preg_replace(array('/-*\.-*/', '/\.{2,}/'), '.', $filename);
        // rendi tutto minuscolo
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        return trim($filename, '.-');
    }
}