<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$datipersonali_template = $template_engine->load_template("dati-personali-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$datipersonali_template->insert("menu", build_menu());

if(isset($_POST['submit-public-post'])){
    $post = $_POST['text'];
    $db->inserisci_post($post, $_SESSION['Username']);
    header("Location: index.php");
}

$datipersonali_template->insert("mioprofilo", build_mioprofilo($username));

$datipersonali_template->insert("header", build_header());
$datipersonali_template->insert("goback", build_goback());
$datipersonali_template->insert("footer", build_footer());


echo $datipersonali_template->build();
