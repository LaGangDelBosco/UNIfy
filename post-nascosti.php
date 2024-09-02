<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$postnascosti_template = $template_engine->load_template("post-nascosti-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: mio-profilo.php");    //da aggiungere messaggio get
        exit();
    }
    else
        echo "Errore nell'eliminazione del post";
}

if(isset($_POST['submit_mostra_post'])){
    $id_post = $_POST['id_post'];
    $db->mostra_post($id_post);
}

$postnascosti_template->insert("menu", build_menu());

$postnascosti_template->insert("post_nascosti", build_post_nascosti($username));

$postnascosti_template->insert("header", build_header());
$postnascosti_template->insert("goback", build_goback());
$postnascosti_template->insert("footer", build_footer());


echo $postnascosti_template->build();