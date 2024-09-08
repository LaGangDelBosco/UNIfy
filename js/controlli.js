/**
 * Funzione che controlla se l'username è nel formato corretto
 * @param {String} username username da controllare
 * @returns {Boolean} true se l'username è nel formato corretto, false altrimenti
 */
function check_username(username){
    // controllo che l'username non contenga caratteri speciali tranne underscore, che sia lungo
    // almeno 4 caratteri, massimo 20, che non inizi con un numero e non contenga spazi
    var regex = /^[a-zA-Z_][a-zA-Z0-9_]{3,19}$/;
    return regex.test(username);
}

/**
 * Funzione che controlla se il nome è nel formato corretto
 * @param {String} nome nome da controllare
 * @returns {Boolean} true se il nome è nel formato corretto, false altrimenti
 */
function check_nome_cognome(nome_cognome){
    // controllo che il nome non contenga caratteri speciali, che sia lungo
    // almeno 2 caratteri, massimo 50
    var regex = /^[A-Z][a-zA-Z\s]{1,49}$/;
    return regex.test(nome);
}

/**
 * Funzione che controlla se la data di nascita è nel formato corretto
 * @param {String} dnascita data di nascita da controllare
 * @returns {Boolean} true se la data di nascita è nel formato corretto, false altrimenti
 */
function check_dnascita(dnascita){
    var regex = /^\d{2}-\d{2}-\d{4}$/;
    return regex.test(dnascita);
}

/**
 * Funzione che controlla se l'email è nel formato corretto
 * @param {String} email email da controllare
 * @returns {Boolean} true se l'email è nel formato corretto, false altrimenti
 */
function check_email(email){
    var regex = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return regex.test(email);
}

/**
 * Funzione che controlla se la password è nel formato corretto
 * @param {String} password password da controllare
 * @returns {Boolean} true se la password è nel formato corretto, false altrimenti
 */
function check_password(password){
    // controllo che la password non contenga spazi, che sia lunga almeno 4 caratteri
    // e massimo 20
    var regex = /^[^\s]{4,20}$/;
    return regex.test(password);
}

/**
 * Funzione che controlla che il testo non sia vuoto e che abbia al massimo 1000 caratteri e non contiene caratteri speciali tranne quelli elencati nella regex e le lettere accentate
 * @param {String} testo testo da controllare
 * @returns {Boolean} true se il testo non è vuoto e ha al massimo 1000 caratteri, false altrimenti
 */
function check_testo(testo){
    var regex = /^[a-zA-Z0-9\s,.:"';!?àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,1000}$/;
    return regex.test(testo);    
}

/**
 * Funzione che controlla che il genere sia una lettera (M o F) o che sia la stringa "Non specificato"
 * @param {String} genere genere da controllare
 * @returns {Boolean} true se il genere è una lettera (M o F) o la stringa "Non specificato", false altrimenti
 */
function check_genere(genere){
    var regex = /^[MF]$/;
    return regex.test(genere) || genere == "Non specificato";
}



/**
 * --------------------------------------------------------------
 */

/**
 * Funzione che controlla se i campi del form aggiungi_recensione in aggiungi.recensione.template.html sono stati riempiti correttamente
 * @returns {Boolean} true se i campi sono stati riempiti correttamente, false altrimenti
 */
function registrazione(){
    let username = document.forms["registrazione_form"]["username"].value;
    let nome_cognome = document.forms["registrazione_form"]["nome_cognome"].value;
    let email = document.forms["registrazione_form"]["email"].value;
    let data_nascita = document.forms["registrazione_form"]["data_nascita"].value;
    let gender = document.forms["registrazione_form"]["gender"].value;
    let password = document.forms["registrazione_form"]["password"].value;

    document.getElementById("username_error").textContent = "";
    document.getElementById("nome_error").textContent = "";
    document.getElementById("email_error").textContent = "";
    document.getElementById("data_nascita_error").textContent = "";
    document.getElementById("gender_error").textContent = "";
    document.getElementById("password_error").textContent = "";
    document.getElementById("conferma_password_error").textContent = "";

    let errors = false;

    if (!check_username(username)) {
        document.getElementById("username_error").textContent = "Username non valido: \n- non deve contenere caratteri speciali ad eccezione di underscore, deve essere lungo tra i 4 e i 20 cartteri, non iniziare con un numero e non contenere spazi";
        errors = true;
    }
    if (!check_nome_cognome(nome_cognome)) {
        document.getElementById("nome_error").textContent = "Nome e cognome non valido: \n- non deve contenere caratteri speciali, deve contenere tra i 2 e i 50 caratteri e non contenere un numero";
        errors = true;
    }
    if (!check_email(email)) {
        document.getElementById("email_error").textContent = "Email non valida: \n - deve essere nel formato corretto";
        errors = true;
    }
    if (!check_dnascita(data_nascita)) {
        document.getElementById("data_nascita_error").textContent = "Data di nascita non valida: \n- deve essere nel formato gg-mm-aaaa";
        errors = true;
    }
    if (!check_genere(gender)){
        document.getElementById("gender_error").textContent = "Genere non valido: \n- deve essere 'M', 'F' oppure 'Non specificato'";
        errors = true;
    }
    if (!check_password(password)){
        document.getElementById("password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        document.getElementById("conferma_password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }

    return !errors
}