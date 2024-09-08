<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$modificaprofilo_template = $template_engine->load_template("modifica-profilo-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$modificaprofilo_template->insert_multiple("menu", build_menu());

$modificaprofilo_template->insert("modifica_profilo", build_modifica_profilo($username));
$modificaprofilo_template->insert("modifica_profilo_mobile", build_modifica_profilo_mobile($username));

$modificaprofilo_template->insert_multiple("suggeriti", build_lista_suggeriti());

$modificaprofilo_template->insert("header", build_header());
$modificaprofilo_template->insert("goback", build_goback());
$modificaprofilo_template->insert("footer", build_footer());

echo $modificaprofilo_template->build();
?>
