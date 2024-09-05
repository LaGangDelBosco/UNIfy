<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$modificadatipersonali_template = $template_engine->load_template("modifica-profilo-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$modificadatipersonali_template->insert("menu", build_menu());

$modificadatipersonali_template->insert("modifica_profilo", build_modifica_profilo($username));

$modificadatipersonali_template->insert("header", build_header());
$modificadatipersonali_template->insert("goback", build_goback());
$modificadatipersonali_template->insert("footer", build_footer());

echo $modificadatipersonali_template->build();
?>
