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
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    if($db->modifica_dati_personali($username, $nome, $email, $gender, $birthdate)) {
        header("Location: ./dati-personali.php?messaggio=Dati modificati con successo");
        exit();
    }
    else{
        header("Location: ./dati-personali.php?messaggio=Errore nella modifica dei dati");
        exit();
    }

}


$datipersonali_template = $template_engine->load_template("dati-personali-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$datipersonali_template->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nella modifica dei dati")
        $datipersonali_template->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $datipersonali_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $datipersonali_template->insert_multiple("messaggio", "");

$datipersonali_template->insert_multiple("datipersonali", build_datipersonali($username));

$datipersonali_template->insert_multiple("suggeriti", build_lista_suggeriti());

$datipersonali_template->insert("header", build_header());
$datipersonali_template->insert("goback", build_goback());
$datipersonali_template->insert("footer", build_footer());


echo $datipersonali_template->build();
