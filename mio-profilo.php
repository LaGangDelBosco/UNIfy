<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

$index_template = $template_engine->load_template("index-template.html");

$username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$index_template->insert("menu", build_menu());

$index_template->insert("mioprofilo", build_mioprofilo($username));

$index_template->insert("header", build_header());
$index_template->insert("goback", build_goback());
$index_template->insert("footer", build_footer());


echo $index_template->build();