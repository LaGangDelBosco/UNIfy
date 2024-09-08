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

if($utente_profilo == $username){
    header("Location: mio-profilo.php");
}

if(isset($_POST['submit_elimina_post'])){       //TODO: ha sendo? nel profilo di un utente non elimino nessun post. semmai l'admin lo nasconde
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: profilo.php?user=$utente_profilo&messaggio=Post eliminato con successo");    //da aggiungere messaggio get
        exit();
    }
    else{
        header("Location: profilo.php?user=$utente_profilo&messaggio=Errore nell'eliminazione del post");
        exit();
    }
}

if($db->get_dati_utente_profilo($utente_profilo)){
    $datiutente = $db->get_dati_utente_profilo($utente_profilo);
    $mioprofilo_template->insert_multiple("immagine", $datiutente['profile_picture_path']);
    $mioprofilo_template->insert_multiple("nome", $datiutente['name']);
    $mioprofilo_template->insert_multiple("email", $datiutente['email']);
    $mioprofilo_template->insert_multiple("birthdate", $datiutente['birthdate']);
    $mioprofilo_template->insert_multiple("username", $utente_profilo);
    $mioprofilo_template->insert_multiple("biografia", $datiutente['bio']);
    $mioprofilo_template->insert_multiple("luogo", $datiutente['location']);
    $mioprofilo_template->insert_multiple("sito", $datiutente['website']);
}
else
    echo "Errore nel caricamento dei dati utente";

$amicizia_info = $db->check_amicizia($username, $utente_profilo);

if(!$amicizia_info){
    $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"submit_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di invio richiesta di amicizia\">Invia richiesta di amicizia</button>");
}else{
    if($amicizia_info['status'] == 'pending'){
        if($amicizia_info['username_1'] == $username){
            $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di annullamento richiesta di amicizia\">Annulla richiesta di amicizia</button>");
        }else{
            $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"accept_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di accettazione richiesta di amicizia\">Accetta richiesta di amicizia</button>
                                                                <button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di rifiuto richiesta di amicizia\">Rifiuta richiesta di amicizia</button>");
        }
    }else{
        $mioprofilo_template->insert_multiple("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di rimozione profilo dagli amici\">Rimuovi dagli amici</button>");
    }
}

if($_SESSION['Username'] == "admin"){
    $mioprofilo_template->insert_multiple("ban_button", "<form method=\"post\" action=\"utenti-banditi.php\">
                                            <input type=\"hidden\" name=\"username\" value=\"$utente_profilo\">
                                            <button class=\"interact\" name=\"submit_ban\" id=\"ban_button\" type=\"submit\" aria-label=\"Bottone di ban utente\">Bandisci utente</button>
                                            </form>");
} else
{
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