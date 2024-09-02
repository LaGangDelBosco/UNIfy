<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['Username'] != "admin"){
    header("Location: error.php?error=403");
    exit();
}

$username = $_SESSION['Username'];

if(isset($_POST['submit_rimuovi_ban'])){
    $username = $_POST['username'];
    $db->remove_user_ban($username);
}

if(isset($_POST['submit_ban'])){
    $username = $_POST['username'];
    $reason = "TEST"; // FIXME da sistemare
    $db->ban_user($username, $reason);
    header("Location: profilo.php?user=$username");
}

$utenti_banditi_template = $template_engine->load_template("utenti-banditi-template.html");

#$index_template->insert("build_keywords", build_keywords());
$utenti_banditi_template->insert("menu", build_menu());

$utenti_banditi_template->insert("lista_utenti_banditi", build_lista_utenti_banditi());

$utenti_banditi_template->insert("header", build_header());
$utenti_banditi_template->insert("goback", build_goback());
$utenti_banditi_template->insert("footer", build_footer());


echo $utenti_banditi_template->build();