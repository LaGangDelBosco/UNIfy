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

$mioprofilo_template->insert("menu", build_menu());

$mioprofilo_template->insert("post", build_mypost($utente_profilo));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();