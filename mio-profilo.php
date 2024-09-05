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
    if($db->elimina_post($id_post)){
        header("Location: mio-profilo.php?messaggio=Post eliminato con successo");
        exit();
    }
    else
        header("Location: mio-profilo.php?messaggio=Errore nell'eliminazione del post");
}

if($db->get_dati_utente_profilo($username)){
    $datiutente = $db->get_dati_utente_profilo($username);
    $mioprofilo_template->insert("immagine", $datiutente['profile_picture_path']);
    $mioprofilo_template->insert("nome", $datiutente['name']);
    $mioprofilo_template->insert("email", $datiutente['email']);
    $mioprofilo_template->insert("birthdate", $datiutente['birthdate']);
    $mioprofilo_template->insert("username", $username);
    $mioprofilo_template->insert("biografia", $datiutente['bio']);
    $mioprofilo_template->insert("luogo", $datiutente['location']);
    $mioprofilo_template->insert_multiple("sito", $datiutente['website']);
}
else   
    echo "Errore nel caricamento dei dati utente";

if(isset($_POST['submit_nascondi_post'])){
    $id_post = $_POST['id_post'];
    $current_page = $_POST['current_page'];
    $db->nascondi_post($id_post);
    header("Location: $current_page?messaggio=Post nascosto con successo");
    exit();
}

if(isset($_POST['submit_modifica_profilo'])){
    $bio = contrassegnaParoleInglesi($_POST['bio']);
    $location = $_POST['location'];
    $website = $_POST['website'];
    if($_FILES['profile_picture_path']['size'] > 0){
        $profile_picture_path = $_FILES['profile_picture_path']['tmp_name'];
        $profile_picture_name = $_FILES['profile_picture_path']['name'];
        $profile_picture_size = $_FILES['profile_picture_path']['size'];
        $profile_picture_type = $_FILES['profile_picture_path']['type'];
        $profile_picture_error = $_FILES['profile_picture_path']['error'];
        $profile_picture = file_get_contents($profile_picture_path);
        $profile_picture = "./media/profile-pictures/" . $username . "_" . time() . "_" . $profile_picture_name;
        if(!move_uploaded_file($profile_picture_path, $profile_picture))
            $profile_picture = null;
    }
    else
        $profile_picture = null;

    if($profile_picture != null) {
        if($db->modifica_profilo($username, $bio, $location, $website, $profile_picture)) {
            header("Location: ./mio-profilo.php?messaggio=Dati modificati con successo");
            exit();
        }
        else {
            header("Location: ./mio-profilo.php?messaggio=Errore nella modifica dei dati");
            exit();
        }
    }
    if($db->modifica_profilo($username, $bio, $location, $website)) {
        header("Location: ./mio-profilo.php?messaggio=Dati modificati con successo");
        exit();
    }
    else{
        header("Location: ./mio-profilo.php?messaggio=Errore nella modifica dei dati");
        exit();
    }
}

$mioprofilo_template->insert("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione del post")
        $mioprofilo_template->insert("messaggio", "<div id='messaggioerrore'>" . $messaggio . "</div>");
    else
        $mioprofilo_template->insert("messaggio", "<div id='messaggio'>" . $messaggio . "</div>");
}else
    $mioprofilo_template->insert("messaggio", "");

$mioprofilo_template->insert("post", build_mypost($username));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();