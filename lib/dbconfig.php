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

        $profile_picture_path = "./media/profile-pictures/default.jpg";

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
            // prepara la query per inserire i dati nella tabella profile
            $query_profile = "INSERT INTO profile (username, profile_picture_path, bio, location, website, created_at, updated_at) VALUES (?, ?, '', '', '', NOW(), NOW())";
            $parameters_profile = array("ss", $username, $profile_picture_path);

            // prepara lo statement
            $stmt_profile = $this->apriconn()->prepare($query_profile);
            if ($stmt_profile === false) {
                $this->err_code = true;
                $this->err_text = "Errore nella preparazione della richiesta per il profilo";
                return false;
            }

            // associa i parametri della query
            $stmt_profile->bind_param(...$parameters_profile);
            // esegue la query
            $stmt_profile->execute();

            // controlla se l'inserimento del profilo è avvenuto con successo
            if ($stmt_profile->affected_rows > 0) {
                $this->err_code = false;
            } else {
                $this->err_code = true;
                $this->err_text = "Errore durante l'inserimento del profilo";
            }

            // chiude lo statement del profilo
            $stmt_profile->close();
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
    public function inserisci_post($testo, $utente, $media_path = null){ 

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
    public function modifica_dati_personali($username, $nome, $email, $gender, $birthdate){
        $conn = $this->apriconn();

        $query = "UPDATE user SET name = ?, email = ?, birthdate = ?, gender = ?, updated_at = NOW() WHERE username = ?";
        $parameters = array("sssss", $nome, $email, $birthdate, $gender, $username);

        // prepara lo statement
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);
 
        // esegue la query
        $stmt->execute();

        // controlla se la modifica è avvenuta con successo

        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la modifica dei dati";
        }

        // chiude lo statement
        $stmt->close();

        // ritorna il risultato
        return !$this->err_code;
    }


    /**
     * Funzione che gestisce la modifica del profilo dell'utente
     * @param $username string username dell'utente
     * @param $bio string bio dell'utente
     * @param $location string luogo dell'utente
     * @param $website string sito web dell'utente
     * @param $profile_picture_path string path dell'immagine del profilo
     * @return bool vero se il profilo è stato modificato, falso altrimenti
     */
    public function modifica_profilo($username, $bio, $luogo, $sito, $corso_studi, $profile_picture = null){
        $conn = $this->apriconn();

        if($profile_picture != null) {
            $query = "UPDATE profile SET corso_studi= ? , bio = ?, location = ?, website = ?, profile_picture_path = ?, updated_at = NOW() WHERE username = ?";
            $parameters = array("ssssss", $corso_studi, $bio, $luogo, $sito, $profile_picture, $username);
        } else {
            $query = "UPDATE profile SET corso_studi = ?, bio = ?, location = ?, website = ?, updated_at = NOW() WHERE username = ?";
            $parameters = array("sssss", $corso_studi, $bio, $luogo, $sito, $username);
        }

        // prepara lo statement
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            $this->err_code = true;
            $this->err_text = "Errore nella preparazione della richiesta";
            return false;
        }

        // associa i parametri della query
        $stmt->bind_param(...$parameters);

        // esegue la query
        $stmt->execute();

        // controlla se la modifica è avvenuta con successo
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la modifica del profilo";
        }

        // chiude lo statement
        $stmt->close();

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
        $query = "SELECT u.name, u.email, u.birthdate, p.* FROM user u LEFT JOIN profile p ON u.username = p.username WHERE u.username = ?";
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

    public function check_amicizia($mittente, $destinatario){
        $conn = $this->apriconn();
        $query = "SELECT status, username_1 FROM friendship WHERE (username_1 = ? AND username_2 = ?) OR (username_1 = ? AND username_2 = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $mittente, $destinatario, $destinatario, $mittente);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    public function invia_richiesta_amicizia($mittente, $destinatario){
        $conn = $this->apriconn();
        $query = "INSERT INTO friendship (username_1, username_2, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $mittente, $destinatario);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'invio della richiesta di amicizia";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function accetta_amicizia($mittente, $destinatario){
        $conn = $this->apriconn();
        $query = "UPDATE friendship SET status = 'accepted' WHERE username_1 = ? AND username_2 = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $destinatario, $mittente);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'accettazione della richiesta di amicizia";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function elimina_amicizia($mittente, $destinatario){
        $conn = $this->apriconn();
        $query = "DELETE FROM friendship WHERE (username_1 = ? AND username_2 = ?) OR (username_1 = ? AND username_2 = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $mittente, $destinatario, $destinatario, $mittente);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la cancellazione dell'amicizia";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function vendi_libro($venditore, $titolo, $autore, $categoria, $anno, $descrizione, $prezzo, $immagine = null){
        $conn = $this->apriconn();

        if($immagine != null) {
            $query = "INSERT INTO book (username, title, author, genre, year, description, cover_path, created_at, updated_at, price) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssss", $venditore, $titolo, $autore, $categoria, $anno, $descrizione, $immagine, $prezzo);
        } else {
            $query = "INSERT INTO book (username, title, author, genre, year, description, created_at, updated_at, price) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $venditore, $titolo, $autore, $categoria, $anno, $descrizione, $prezzo);
        }
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la vendita del libro";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_annuncio($id_annuncio){
        $conn = $this->apriconn();
        $query = "SELECT * FROM book WHERE book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_annuncio);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    public function delete_annuncio($id_annuncio){
        $conn = $this->apriconn();
        $query = "DELETE FROM book WHERE book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_annuncio);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione dell'annuncio";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_chat_message($id_annuncio, $sender_username, $receiver_username) {
        $conn = $this->apriconn();
        $query = "SELECT * FROM chat_message WHERE id_annuncio = ? AND ((sender_username = ? AND receiver_username = ?) OR (sender_username = ? AND receiver_username = ?)) ORDER BY timestamp ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $id_annuncio, $sender_username, $receiver_username, $receiver_username, $sender_username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function send_chat_message($id_annuncio, $sender_username, $receiver_username, $message) {
        $conn = $this->apriconn();
        $query = "INSERT INTO chat_message (id_annuncio, sender_username, receiver_username, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $id_annuncio, $sender_username, $receiver_username, $message);
        $stmt->execute();
    }

    public function ban_user($username, $reason){
        $time_now = time();
        $formatted_time = date("Y-m-d H:i:s", $time_now);
        $conn = $this->apriconn();
        $query = "UPDATE user SET banned = 1, ban_reason = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $reason, $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante il ban dell'utente";
        }
        $stmt->close();
        $conn->close();
        $this->delete_aula_created_by($username);
        return !$this->err_code;
    }

    public function remove_user_ban($username)
    {
        $conn = $this->apriconn();
        $query = "UPDATE user SET banned = 0 WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la rimozione del ban dell'utente";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function check_ban($username){
        $conn = $this->apriconn();
        $query = "SELECT banned FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data['banned'];
    }

    public function nascondi_post($post_id){
        $conn = $this->apriconn();
        $query = "UPDATE post SET hidden = TRUE WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la cancellazione del post";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function mostra_post($post_id){
        $conn = $this->apriconn();
        $query = "UPDATE post SET hidden = FALSE WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la cancellazione del post";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function delete_aula($aula_id){
        $conn = $this->apriconn();
        $query = "DELETE FROM room WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $aula_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione dell'aula";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_aula($aula_id){
        $conn = $this->apriconn();
        $query = "SELECT * FROM room WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $aula_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    public function delete_aula_created_by($user) {
        $conn = $this->apriconn();
        $query = "DELETE FROM room WHERE created_by = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione dell'aula";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_room_chat_message($aula_id) {
        $conn = $this->apriconn();
        $query = "SELECT * FROM room_message WHERE room_code = ? ORDER BY timestamp ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $aula_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function crea_aula($nome, $categoria, $username){
        $conn = $this->apriconn();
        $query = "INSERT INTO room (name, genre, created_at, created_by) VALUES (?, ?, NOW(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $nome, $categoria, $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante la creazione dell'aula";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function elimina_notifica($id_notifica){
        $conn = $this->apriconn();

        $query_username = "SELECT receiver_username FROM notification WHERE notification_id = ?";
        $stmt_username = $conn->prepare($query_username);
        $stmt_username->bind_param("i", $id_notifica);
        $stmt_username->execute();
        $result_username = $stmt_username->get_result();
        $data_username = $result_username->fetch_assoc();
        $stmt_username->close();
        $username = $data_username['receiver_username']; 

        $query = "DELETE FROM notification WHERE notification_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_notifica);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
            $this->decrementa_numero_notifiche($username);
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione della notifica";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function decrementa_numero_notifiche($username){
        $conn = $this->apriconn();
        $query = "UPDATE user SET notifications_amount = notifications_amount - 1 WHERE username = ? AND notifications_amount > 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'aggiornamento del numero di notifiche";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function get_numero_notifiche($username){
        $conn = $this->apriconn();
        $query = "SELECT notifications_amount FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data['notifications_amount'];
    }

    public function elimina_tutte_notifiche($username){
        $conn = $this->apriconn();
        $query = "DELETE FROM notification WHERE receiver_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
            $this->azzera_numero_notifiche($username);
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'eliminazione delle notifiche";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function azzera_numero_notifiche($username){
        $conn = $this->apriconn();
        $query = "UPDATE user SET notifications_amount = 0 WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $this->err_code = false;
        } else {
            $this->err_code = true;
            $this->err_text = "Errore durante l'aggiornamento del numero di notifiche";
        }
        $stmt->close();
        $conn->close();
        return !$this->err_code;
    }

    public function check_username($username) : bool {
        $conn = $this->apriconn();
        $query = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return !empty($data);
    }

    public function get_amici($username){
        $conn = $this->apriconn();
        $query = "SELECT username_1, username_2 FROM friendship f 
              JOIN user u1 ON f.username_1 = u1.username 
              JOIN user u2 ON f.username_2 = u2.username 
              WHERE (f.username_1 = ? OR f.username_2 = ?) 
              AND f.status = 'accepted' 
              AND u1.banned = 0 
              AND u2.banned = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public function get_amici_in_comune($friend){
        $conn = $this->apriconn();
        $query = "SELECT username_1, username_2 FROM friendship f 
              JOIN user u1 ON f.username_1 = u1.username 
              JOIN user u2 ON f.username_2 = u2.username 
              WHERE (f.username_1 = ? OR f.username_2 = ?) 
              AND f.status = 'accepted' 
              AND u1.banned = 0 
              AND u2.banned = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $friend, $friend);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public function get_corso_studi($username){
        $conn = $this->apriconn();
        $query = "SELECT corso_studi FROM profile WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data['corso_studi'];
    }

    public function get_utenti_corso_studi($corso_studi){
        $conn = $this->apriconn();
        $query = "SELECT username FROM profile WHERE corso_studi = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $corso_studi);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result;
    }
}