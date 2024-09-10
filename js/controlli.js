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
    var regex = /^[A-Z][a-z]{1,49}(?:\s[A-Z][a-z]{1,49})*$/;
    return regex.test(nome_cognome);
}

/**
 * Funzione che controlla se la data di nascita è nel formato corretto
 * @param {String} dnascita data di nascita da controllare
 * @returns {Boolean} true se la data di nascita è nel formato corretto, false altrimenti
 */
function check_dnascita(dnascita){
    var regex = /^\d{4}-\d{2}-\d{2}$/;
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
 * Funzione che controlla che il genere sia una lettera (M o F) o che sia la stringa "Non specificato"
 * @param {String} genere genere da controllare
 * @returns {Boolean} true se il genere è una lettera (M o F) o la stringa "Non specificato", false altrimenti
 */
function check_genere(genere){
    var regex = /^[MF]$/;
    return regex.test(genere) || genere == "Non specificato";
}

/**
 * Funzione che controlla se il testo è nel formato corretto
 * @param {String} text testo da controllare
 * @returns {Boolean} true se il testo è nel formato corretto, false altrimenti
 */
function check_text(text){
    // controllo che il testo non contenga caratteri speciali, che sia lungo tra i 3 e i 300 caratteri
    var regex = /^[a-zA-Z0-9\s,.:"';!?àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{3,300}$/;
    return regex.test(text);
}

/**
 * Funzione che controlla se la ricerca è nel formato corretto
 * @param {String} search ricerca da controllare
 * @returns {Boolean} true se la ricerca è nel formato corretto, false altrimenti
 */
function check_search(search){
    // controllo che la ricerca non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(search);
}

function check_nome_aula(nome){
    // controllo che il nome non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(nome);
}

function check_genre_aula(aula){
    // controllo che l'aula non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(aula);
}

function check_titolo(titolo){
    // controllo che il titolo non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(titolo);
}

function check_autore(autore){
    // controllo che l'autore non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(autore);
}

function check_categoria(categoria){
    // controllo che la categoria non contenga caratteri speciali, che sia lunga tra 1 e 50 caratteri
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,50}$/;
    return regex.test(categoria);
}

function check_anno(anno){
    // controllo che l'anno sia composto da 4 cifre comprese tra 0 e 9
    var regex = /^\d{4}$/;
    return regex.test(anno);
}

function check_prezzo(prezzo){
    // controllo che il prezzo sia composto da cifre e da un punto
    var regex = /^\d+(\.\d{1,2})?$/;
    return regex.test(prezzo);
}

function check_luogo(luogo){
    var regex = /^[a-zA-Z0-9\sàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸ\[\]\/]{1,100}$/;
    return regex.test(luogo);
}

function check_website(website){
    // controllo che il website sia un url
    var regex = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[a-zA-Z0-9-._~:\/?#[\]@!$&'()*+,;=]*)?$/;;
    return regex.test(website);
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
        document.getElementById("data_nascita_error").textContent = "Data di nascita non valida: \n- deve essere nel formato gg/mm/aaaa";
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

function login(){
    let username = document.forms["login_form"]["username"].value;
    let password = document.forms["login_form"]["password"].value;

    document.getElementById("username_error").textContent = "";
    document.getElementById("password_error").textContent = "";

    let errors = false;

    if (!check_username(username)) {
        document.getElementById("username_error").textContent = "Username non valido: \n- non deve contenere caratteri speciali ad eccezione di underscore, deve essere lungo tra i 4 e i 20 cartteri, non iniziare con un numero e non contenere spazi";
        errors = true;
    }
    if (!check_password(password)){
        document.getElementById("password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }

    return !errors
}

function post(){
    let text = document.forms["post_form"]["text"].value;
    
    document.getElementById("text_error").textContent = "";

    let errors = false;

    if (!check_text(text)){
        document.getElementById("text_error").textContent = "Testo non valido: \n- non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }

    return !errors
}

function postmobile(){
    let text = document.forms["post_form_mobile"]["text_mobile"].value;
    
    document.getElementById("text_error_mobile").textContent = "";

    let errors = false;

    if (!check_text(text)){
        document.getElementById("text_error_mobile").textContent = "Testo non valido: \n- non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }

    return !errors
}

function comment(post_id, comment){
    document.getElementById(`comment_error_${post_id}`).textContent = "";

    let errors = false;

    if (!check_text(comment)){
        document.getElementById(`comment_error_${post_id}`).textContent = "Commento non valido: \n- non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }

    return !errors
}

function searchbar(){
    let search = document.forms["searchbar_form"]["q"].value;

    document.getElementById("search_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("search_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function searchbarmobile(){
    let search = document.forms["searchbarmobile_form"]["q"].value;

    document.getElementById("searchmobile_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("searchmobile_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function searchaule(){
    let search = document.forms["search_aule"]["search_aula"].value;

    document.getElementById("searchaula_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("searchaula_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function searchaulemobile(){
    let search = document.forms["search_aule_mobile"]["search_aula_mobile"].value;

    document.getElementById("searchaulamobile_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("searchaulamobile_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function creaaula(){
    let name = document.forms["crea_aula_form"]["name"].value;
    let genre = document.forms["crea_aula_form"]["genre"].value;

    document.getElementById("name_error").textContent = "";
    document.getElementById("genre_error").textContent = "";

    let errors = false;

    if (!check_nome_aula(name)){
        document.getElementById("name_error").textContent = "Nome non valido: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_genre_aula(genre)){
        document.getElementById("genre_error").textContent = "Categoria non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function searchlibri(){
    let search = document.forms["search_libri"]["search_libro"].value;

    document.getElementById("searchlibro_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("searchlibro_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function searchlibrimobile(){
    let search = document.forms["search_libri_mobile"]["search_libro_mobile"].value;

    document.getElementById("searchlibromobile_error").textContent = "";

    let errors = false;

    if (!check_search(search)){
        document.getElementById("searchlibromobile_error").textContent = "Ricerca non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }

    return !errors
}

function vendilibro(){
    let titolo = document.forms["vendi_libro_form"]["titolo"].value;
    let autore = document.forms["vendi_libro_form"]["autore"].value;
    let categoria = document.forms["vendi_libro_form"]["categoria"].value;
    let anno = document.forms["vendi_libro_form"]["anno"].value;
    let descrizione = document.forms["vendi_libro_form"]["descrizione"].value;
    let prezzo = document.forms["vendi_libro_form"]["prezzo"].value;

    document.getElementById("titolo_error").textContent = "";
    document.getElementById("autore_error").textContent = "";
    document.getElementById("categoria_error").textContent = "";
    document.getElementById("anno_error").textContent = "";
    document.getElementById("descrizione_error").textContent = "";
    document.getElementById("prezzo_error").textContent = "";

    let errors = false;

    if (!check_titolo(titolo)){
        document.getElementById("titolo_error").textContent = "Titolo non valido: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_autore(autore)){
        document.getElementById("autore_error").textContent = "Autore non valido: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_categoria(categoria)){
        document.getElementById("categoria_error").textContent = "Categoria non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_anno(anno)){
        document.getElementById("anno_error").textContent = "Anno non valido: \n deve essere composto da 4 cifre";
        errors = true;
    }
    if (!check_text(descrizione)){
        document.getElementById("descrizione_error").textContent = "Descrizione non valida: \n non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }
    if (!check_prezzo(prezzo)){
        document.getElementById("prezzo_error").textContent = "Prezzo non valido: \n deve essere composto da cifre e da un punto";
        errors = true;
    }

    return !errors
}

function vendilibromobile(){
    let titolo = document.forms["vendi_libro_form_mobile"]["titolo_mobile"].value;
    let autore = document.forms["vendi_libro_form_mobile"]["autore_mobile"].value;
    let categoria = document.forms["vendi_libro_form_mobile"]["categoria_mobile"].value;
    let anno = document.forms["vendi_libro_form_mobile"]["anno_mobile"].value;
    let descrizione = document.forms["vendi_libro_form_mobile"]["descrizione_mobile"].value;
    let prezzo = document.forms["vendi_libro_form_mobile"]["prezzo_mobile"].value;

    document.getElementById("titolo_mobile_error").textContent = "";
    document.getElementById("autore_mobile_error").textContent = "";
    document.getElementById("categoria_mobile_error").textContent = "";
    document.getElementById("anno_mobile_error").textContent = "";
    document.getElementById("descrizione_mobile_error").textContent = "";
    document.getElementById("prezzo_mobile_error").textContent = "";

    let errors = false;

    if (!check_titolo(titolo)){
        document.getElementById("titolo_mobile_error").textContent = "Titolo non valido: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_autore(autore)){
        document.getElementById("autore_mobile_error").textContent = "Autore non valido: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_categoria(categoria)){
        document.getElementById("categoria_mobile_error").textContent = "Categoria non valida: \n non deve contenere caratteri speciali e deve essere lungo tra 1 e 50 cartteri";
        errors = true;
    }
    if (!check_anno(anno)){
        document.getElementById("anno_mobile_error").textContent = "Anno non valido: \n deve essere composto da 4 cifre";
        errors = true;
    }
    if (!check_text(descrizione)){
        document.getElementById("descrizione_mobile_error").textContent = "Descrizione non valida: \n non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }
    if (!check_prezzo(prezzo)){
        document.getElementById("prezzo_mobile_error").textContent = "Prezzo non valido: \n deve essere composto da cifre e da un punto";
        errors = true;
    }

    return !errors
}

function modificaprofilo(){
    let bio = document.forms["modifica_profilo"]["bio"].value;
    let location = document.forms["modifica_profilo"]["location"].value;
    let website = document.forms["modifica_profilo"]["website"].value;

    document.getElementById("bio_error").textContent = "";
    document.getElementById("location_error").textContent = "";
    document.getElementById("website_error").textContent = "";

    let errors = false;

    if (!check_text(bio)){
        document.getElementById("bio_error").textContent = "Biografia non valida: \n non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }
    if (!check_luogo(location)){
        document.getElementById("location_error").textContent = "Luogo non valida: \n non deve contenere caratteri speciali e deve essere lungo tra i 3 e i 100 cartteri";
        errors = true;
    }
    if (!check_titolo(website)){
        document.getElementById("website_error").textContent = "Sito Web non valido: \n deve essere nella forma di un url";
        errors = true;
    }
}

function modificaprofilomobile(){
    let bio = document.forms["modifica_profilo_mobile"]["bio_mobile"].value;
    let location = document.forms["modifica_profilo_mobile"]["location_mobile"].value;
    let website = document.forms["modifica_profilo_mobile"]["website_mobile"].value;

    document.getElementById("bio_mobile_error").textContent = "";
    document.getElementById("location_mobile_error").textContent = "";
    document.getElementById("website_mobile_error").textContent = "";

    let errors = false;

    if (!check_text(bio)){
        document.getElementById("bio_mobile_error").textContent = "Biografia non valida: \n non deve contenere caratteri speciali ad eccezione di , . : \" \' ; ! ?  deve essere lungo tra i 3 e i 300 cartteri";
        errors = true;
    }
    if (!check_luogo(location)){
        document.getElementById("location_mobile_error").textContent = "Luogo non valida: \n non deve contenere caratteri speciali e deve essere lungo tra i 3 e i 100 cartteri";
        errors = true;
    }
    if (!check_titolo(website)){
        document.getElementById("website_mobile_error").textContent = "Sito Web non valido: \n deve essere nella forma di un url";
        errors = true;
    }
}

function modificadatipersonali(){
    let nome_cognome = document.forms["modifica_dati_personali"]["name"].value;
    let email = document.forms["modifica_dati_personali"]["email"].value;
    let data_nascita = document.forms["modifica_dati_personali"]["birthdate"].value;
    let genere = document.forms["modifica_dati_personali"]["gender"].value;

    document.getElementById("name_error").textContent = "";
    document.getElementById("email_error").textContent = "";
    document.getElementById("birthdate_error").textContent = "";
    document.getElementById("gender_error").textContent = "";

    let errors = false;

    if (!check_nome_cognome(nome_cognome)) {
        document.getElementById("name_error").textContent = "Nome e cognome non valido: \n- non deve contenere caratteri speciali, deve contenere tra i 2 e i 50 caratteri e non contenere un numero";
        errors = true;
    }
    if (!check_email(email)) {
        document.getElementById("email_error").textContent = "Email non valida: \n - deve essere nel formato corretto";
        errors = true;
    }
    if (!check_dnascita(data_nascita)) {
        document.getElementById("birthdate_error").textContent = "Data di nascita non valida: \n- deve essere nel formato gg/mm/aaaa";
        errors = true;
    }
    if (!check_genere(genere)){
        document.getElementById("gender_error").textContent = "Genere non valido: \n- deve essere 'M', 'F' oppure 'Non specificato'";
        errors = true;
    }

    return !errors
}

function modificadatipersonalimobile(){
    let nome_cognome = document.forms["modifica_dati_personali_mobile"]["name_mobile"].value;
    let email = document.forms["modifica_dati_personali_mobile"]["email_mobile"].value;
    let data_nascita = document.forms["modifica_dati_personali_mobile"]["birthdate_mobile"].value;
    let genere = document.forms["modifica_dati_personali_mobile"]["gender_mobile"].value;

    document.getElementById("name_mobile_error").textContent = "";
    document.getElementById("email_mobile_error").textContent = "";
    document.getElementById("birthdate_mobile_error").textContent = "";
    document.getElementById("gender_mobile_error").textContent = "";

    let errors = false;

    if (!check_nome_cognome(nome_cognome)) {
        document.getElementById("name_mobile_error").textContent = "Nome e cognome non valido: \n- non deve contenere caratteri speciali, deve contenere tra i 2 e i 50 caratteri e non contenere un numero";
        errors = true;
    }
    if (!check_email(email)) {
        document.getElementById("email_mobile_error").textContent = "Email non valida: \n - deve essere nel formato corretto";
        errors = true;
    }
    if (!check_dnascita(data_nascita)) {
        document.getElementById("birthdate_mobile_error").textContent = "Data di nascita non valida: \n- deve essere nel formato gg/mm/aaaa";
        errors = true;
    }
    if (!check_genere(genere)){
        document.getElementById("gender_mobile_error").textContent = "Genere non valido: \n- deve essere 'M', 'F' oppure 'Non specificato'";
        errors = true;
    }

    return !errors
}

function modificapassword(){
    let old_password = document.forms["edit_password_form"]["old_password"].value;
    let new_password = document.forms["edit_password_form"]["new_password"].value;
    let confirm_password = document.forms["edit_password_form"]["confirm_password"].value;

    document.getElementById("old_password_error").textContent = "";
    document.getElementById("new_password_error").textContent = "";
    document.getElementById("confirm_password_error").textContent = "";

    let errors = false;

    if (!check_password(old_password)){
        document.getElementById("old_password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }
    if (!check_password(new_password)){
        document.getElementById("new_password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        document.getElementById("confirm_password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }
    if (new_password != confirm_password){
        document.getElementById("confirm_password_error").textContent = "Le password non corrispondono";
        errors = true;
    }

    return !errors
}

function modificapasswordmobile(){
    let old_password = document.forms["edit_password_form_mobile"]["old_password_mobile"].value;
    let new_password = document.forms["edit_password_form_mobile"]["new_password_mobile"].value;
    let confirm_password = document.forms["edit_password_form_mobile"]["confirm_password_mobile"].value;

    document.getElementById("old_password_error_mobile").textContent = "";
    document.getElementById("new_password_error_mobile").textContent = "";
    document.getElementById("confirm_password_error_mobile").textContent = "";

    let errors = false;

    if (!check_password(old_password)){
        document.getElementById("old_password_error_mobile").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }
    if (!check_password(new_password)){
        document.getElementById("new_password_error").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        document.getElementById("confirm_password_error_mobile").textContent = "Password non valida: \n- non deve contenere spazi ed essere lunga tra i 4 e i 20 caratteri";
        errors = true;
    }
    if (new_password != confirm_password){
        document.getElementById("confirm_password_error_mobile").textContent = "Le password non corrispondono";
        errors = true;
    }

    return !errors
}