<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$postcommentati_template = $template_engine->load_template("post-commentati-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: post-commentati.php?messaggio=Post eliminato con successo");
        exit();
    }
    else{
        header("Location: post-commentati.php?messaggio=Errore nell'eliminazione del post");
        exit();
    }
}

$postcommentati_template->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione del post")
        $postcommentati_template->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $postcommentati_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $postcommentati_template->insert_multiple("messaggio", "");

$postcommentati_template->insert("post", build_commented_posts($username));
$postcommentati_template->insert("post_mobile", build_commented_posts_mobile($username));

$postcommentati_template->insert_multiple("suggeriti", build_lista_suggeriti());

$postcommentati_template->insert("header", build_header());
$postcommentati_template->insert("goback", build_goback());
$postcommentati_template->insert("footer", build_footer());


echo $postcommentati_template->build();