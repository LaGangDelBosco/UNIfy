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

$mioprofilo_template->insert("menu", build_menu());

$mioprofilo_template->insert("mioprofilo", build_mioprofilo($username));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();