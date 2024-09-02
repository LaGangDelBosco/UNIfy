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

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: mio-profilo.php");    //da aggiungere messaggio get
        exit();
    }
    else
        echo "Errore nell'eliminazione del post";
}

if($db->get_dati_utente_profilo($utente_profilo)){
    $datiutente = $db->get_dati_utente_profilo($utente_profilo);
    $mioprofilo_template->insert("immagine", $datiutente['profile_picture_path']);
    $mioprofilo_template->insert("nome", $datiutente['name']);
    $mioprofilo_template->insert("email", $datiutente['email']);
    $mioprofilo_template->insert("birthdate", $datiutente['birthdate']);
    $mioprofilo_template->insert_multiple("username", $utente_profilo);
}
else
    echo "Errore nel caricamento dei dati utente";

$amicizia_info = $db->check_amicizia($username, $utente_profilo);

if(!$amicizia_info){
    $mioprofilo_template->insert("friendship_button", "<button class=\"interact\" name=\"submit_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di invio richiesta di amicizia\">Invia richiesta di amicizia</button>");
}else{
    if($amicizia_info['status'] == 'pending'){
        if($amicizia_info['username_1'] == $username){
            $mioprofilo_template->insert("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di annullamento richiesta di amicizia\">Annulla richiesta di amicizia</button>");
        }else{
            $mioprofilo_template->insert("friendship_button", "<button class=\"interact\" name=\"accept_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di accettazione richiesta di amicizia\">Accetta richiesta di amicizia</button>
                                                                <button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di rifiuto richiesta di amicizia\">Rifiuta richiesta di amicizia</button>");
        }
    }else{
        $mioprofilo_template->insert("friendship_button", "<button class=\"interact\" name=\"delete_friendship\" id=\"friend_button\" type=\"submit\" aria-label=\"Bottone di rimozione profilo dagli amici\">Rimuovi dagli amici</button>");
    }
}

if(isset($_POST['submit_friendship'])){
    if($db->invia_richiesta_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo");
        exit();
    }else{
        echo "Errore nell'invio della richiesta di amicizia";
    }
}

if(isset($_POST['delete_friendship'])){
    if($db->elimina_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo");
        exit();
    }else{
        echo "Errore nella cancellazione dell'amicizia";
    }
}

if(isset($_POST['accept_friendship'])){
    if($db->accetta_amicizia($username, $utente_profilo)){
        header("Location: profilo.php?user=$utente_profilo");
        exit();
    }else{
        echo "Errore nell'accettazione dell'amicizia";
    }
}

$mioprofilo_template->insert("menu", build_menu());

$mioprofilo_template->insert("post", build_mypost($utente_profilo));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();