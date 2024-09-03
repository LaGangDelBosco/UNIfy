<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$mioprofilo_template = $template_engine->load_template("post-commentati-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: post-commentati.php?messaggio=Post eliminato con successo");
        exit();
    }
    else
        header("Location: post-commentati.php?messaggio=Errore nell'eliminazione del post");
}

$mioprofilo_template->insert("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione del post")
        $mioprofilo_template->insert("messaggio", "<div id='messaggioerrore'>" . $messaggio . "</div>");
    else
        $mioprofilo_template->insert("messaggio", "<div id='messaggio'>" . $messaggio . "</div>");
}else
    $mioprofilo_template->insert("messaggio", "");

$mioprofilo_template->insert("post", build_commented_posts($username));

$mioprofilo_template->insert("header", build_header());
$mioprofilo_template->insert("goback", build_goback());
$mioprofilo_template->insert("footer", build_footer());


echo $mioprofilo_template->build();