<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_modifica_dati_personali'])){
    $nome = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $location = $_POST['location'];
    $website = $_POST['website'];

    if($db->modifica_dati_personali($username, $nome, $email, $bio, $gender, $birthdate, $location, $website))
        header("Location: ./dati-personali.php?messaggio=Dati modificati con successo");
    else
        header("Location: ./dati-personali.php?messaggio=Errore nella modifica dei dati");
}


$datipersonali_template = $template_engine->load_template("dati-personali-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$datipersonali_template->insert("menu", build_menu());

$datipersonali_template->insert("mioprofilo", build_mioprofilo($username));

$datipersonali_template->insert("header", build_header());
$datipersonali_template->insert("goback", build_goback());
$datipersonali_template->insert("footer", build_footer());


echo $datipersonali_template->build();
