<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['Username'];

$amici_template = $template_engine->load_template("amici-template.html");

#$index_template->insert("build_keywords", build_keywords());
$amici_template->insert("menu", build_menu());

$amici_template->insert("lista_amici", build_lista_amici($username));

$amici_template->insert("header", build_header());
$amici_template->insert("goback", build_goback());
$amici_template->insert("footer", build_footer());


echo $amici_template->build();