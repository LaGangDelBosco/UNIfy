<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$mioprofilo_template = $template_engine->load_template("mio-profilo-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->eliminapost($id_post)){
        header("Location: mio-profilo.php");    //da aggiungere messaggio get
        exit();
    }
    else
        echo "Errore nell'eliminazione del post";
}

if($db->datiutentemioprofilo($username)){
    $datiutente = $db->datiutentemioprofilo($username);
    $mioprofilo_template->insert("immagine", $datiutente['profile_picture_url']);
    $mioprofilo_template->insert("nome", $datiutente['name']);
    $mioprofilo_template->insert("email", $datiutente['email']);
    $mioprofilo_template->insert("birthdate", $datiutente['birthdate']);
    $mioprofilo_template->insert("username", $username);
}
else   
    echo "Errore nel caricamento dei dati utente";

$mioprofilo_template->insert("menu", build_menu());

$mioprofilo_template->insert("post", build_mypost($username));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();