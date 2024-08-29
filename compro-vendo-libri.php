<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$comprovendolibri_template = $template_engine->load_template("compro-vendo-libri-template.html");

$comprovendolibri_template->insert("menu", build_menu());

$comprovendolibri_template->insert("lista_libri", build_lista_libri());

$comprovendolibri_template->insert("header", build_header());
$comprovendolibri_template->insert("goback", build_goback());
$comprovendolibri_template->insert("footer", build_footer());


echo $comprovendolibri_template->build();
