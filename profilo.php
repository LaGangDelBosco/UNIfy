<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$mioprofilo_template = $template_engine->load_template("profilo-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_GET['user']))
    $utente_profilo = $_GET['user'];

if($_GET['user'] == "" || !$db->check_username($utente_profilo)){
    header("Location: error.php?error=400&forced=1");
    exit();
}

if($db->check_ban($utente_profilo) && $_SESSION['Username'] != "admin"){
    header("Location: error.php?error=404&forced=1");
    exit();
}

if($utente_profilo == $username){
    header("Location: mio-profilo.php");
}

if($db->get_dati_utente_profilo($utente_profilo)){
    $datiutente = $db->get_dati_utente_profilo($utente_profilo);
    $mioprofilo_template->insert_multiple("immagine", $datiutente['profile_picture_path'] ?? "media/profile-pictures/default.jpg");
    $mioprofilo_template->insert_multiple("nome", $datiutente['name']);
    $mioprofilo_template->insert_multiple("email", $datiutente['email']);
    $mioprofilo_template->insert_multiple("birthdate", $datiutente['birthdate']);
    $mioprofilo_template->insert_multiple("username", $utente_profilo);
    $mioprofilo_template->insert_multiple("biografia", $datiutente['bio'] ?? "");
    $mioprofilo_template->insert_multiple("luogo", $datiutente['location'] ?? "");
    $mioprofilo_template->insert_multiple("sito", $datiutente['website'] ?? "");
    $mioprofilo_template->insert_multiple("corso_studi", $datiutente['corso_studi'] ?? "");
}
else
    echo "Errore nel caricamento dei dati utente";

$amicizia_info = $db->check_amicizia($username, $utente_profilo);

if(!$amicizia_info){
    $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"submit_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Invia richiesta di amicizia: Bottone di invio richiesta di amicizia\">Invia richiesta di amicizia</button>");
    $mioprofilo_template->insert("friendship_button_mobile", "<button class=\"interact\" name=\"submit_friendship\" id=\"friend_button_mobile\" type=\"submit\" aria-label=\"Invia richiesta di amicizia: Bottone di invio richiesta di amicizia\">Invia richiesta di amicizia</button>");
}else{
    if($amicizia_info['status'] == 'pending'){
        if($amicizia_info['username_1'] == $username){
            $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Annulla richiesta di amicizia: Bottone di annullamento richiesta di amicizia\">Annulla richiesta di amicizia</button>");
            $mioprofilo_template->insert("friendship_button_mobile", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button_mobile\" type=\"submit\" aria-label=\"Annulla richiesta di amicizia: Bottone di annullamento richiesta di amicizia\">Annulla richiesta di amicizia</button>");
        }else{
            $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"accept_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Accetta richiesta di amicizia: Bottone di accettazione richiesta di amicizia\">Accetta richiesta di amicizia</button>
                                                                <button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Rifiuta richiesta di amicizia: Bottone di rifiuto richiesta di amicizia\">Rifiuta richiesta di amicizia</button>");
            $mioprofilo_template->insert("friendship_button_mobile", "<button class=\"interact\" name=\"accept_friendship\" id=\"friend_button_mobile\" type=\"submit\" aria-label=\"Accetta richiesta di amicizia: Bottone di accettazione richiesta di amicizia\">Accetta richiesta di amicizia</button>
                                                                    <button class=\"interact\" name=\"delete_friendship\" id=\"friend_button_mobile\" type=\"submit\" aria-label=\"Rifiuta richiesta di amicizia: Bottone di rifiuto richiesta di amicizia\">Rifiuta richiesta di amicizia</button>");
        }
    }else{
        $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Rimuovi dagli amici: Bottone di rimozione profilo dagli amici\">Rimuovi dagli amici</button>");
        $mioprofilo_template->insert("friendship_button_mobile", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button_mobile\" type=\"submit\" aria-label=\"Rimuovi dagli amici: Bottone di rimozione profilo dagli amici\">Rimuovi dagli amici</button>");
    }
}

if($_SESSION['Username'] == "admin"){
    if($db->check_ban($utente_profilo)) {
        $mioprofilo_template->insert_multiple("ban_button", "<form method=\"post\" action=\"utenti-banditi.php\">
                                            <fieldset>
                                                <legend>Rimuovi ban utente</legend>
                                                <input type=\"hidden\" name=\"username\" value=\"$utente_profilo\" />
                                                <button class=\"interact\" name=\"submit_rimuovi_ban\" id=\"ban_button\" type=\"submit\" aria-label=\"Rimuovi ban utente: Bottone di rimozione ban utente\">Rimuovi ban utente</button>
                                            </fieldset>
                                            </form>");
        $mioprofilo_template->insert("ban_button_mobile", "<form method=\"post\" action=\"utenti-banditi.php\">
                                            <fieldset>
                                                <legend>Rimuovi ban utente</legend>
                                                <input type=\"hidden\" name=\"username\" value=\"$utente_profilo\" />
                                                <button class=\"interact\" name=\"submit_rimuovi_ban\" id=\"ban_button_mobile\" type=\"submit\" aria-label=\"Rimuovi ban utente: Bottone di rimozione ban utente\">Rimuovi ban utente</button>
                                            </fieldset>
                                            </form>");
    } else {
        $mioprofilo_template->insert_multiple("ban_button", "<form id='banForm_$utente_profilo' onsubmit=\"openBanDialog('$utente_profilo'); return false;\">
                    <div>
                        <button id=\"ban_button\" class=\"interact\" type=\"submit\" aria-label=\"Bandisci utente: Bottone che permette di bannare l'utente\">Bandisci utente</button>
                    </div>
                </form>");
        $mioprofilo_template->insert("ban_button_mobile", "<form id='banForm_mobile_$utente_profilo' onsubmit=\"openBanDialog('$utente_profilo'); return false;\">
                    <div>
                        <button id=\"ban_button_mobile\" class=\"interact\" type=\"submit\" aria-label=\"Bandisci utente: Bottone che permette di bannare l'utente\">Bandisci utente</button>
                    </div>
                </form>");
    }
}else{
    $mioprofilo_template->insert_multiple("ban_button", "");
}


if(isset($_POST['submit_friendship'])){
    if($db->invia_richiesta_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo&messaggio=Richiesta di amicizia inviata con successo");
        exit();
    }else{
        header("Location: profilo.php?user=$utente_profilo&messaggio=Errore nell'invio della richiesta di amicizia");
        exit();
    }
}

if(isset($_POST['delete_friendship'])){
    if($db->elimina_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo&messaggio=Amicizia rimossa con successo");
        exit();
    }else{
        header("Location: profilo.php?user=$utente_profilo&messaggio=Errore nella rimozione dell'amicizia");
        exit();
    }
}

if(isset($_POST['accept_friendship'])){
    if($db->accetta_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo&messaggio=Amicizia accettata con successo");
        exit();
    }else{
        header("Location: profilo.php?user=$utente_profilo&messaggio=Errore nell'accettazione dell'amicizia");
        exit();
    }
}

$mioprofilo_template->insert("keywords", $utente_profilo .", ". $datiutente['name']);

$mioprofilo_template->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione del post")
        $mioprofilo_template->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $mioprofilo_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $mioprofilo_template->insert_multiple("messaggio", "");

$mioprofilo_template->insert("post", build_mypost($utente_profilo));
$mioprofilo_template->insert("post_mobile", build_mypost_mobile($utente_profilo));

$mioprofilo_template->insert_multiple("suggeriti", build_lista_suggeriti());

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();