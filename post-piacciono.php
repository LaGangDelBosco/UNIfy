<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$postpiacciono = $template_engine->load_template("post-piacciono-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

if(isset($_POST['submit_elimina_post'])){
    $id_post = $_POST['post_id'];
    if($db->elimina_post($id_post)){
        header("Location: post-piacciono.php?messaggio=Post eliminato con successo");
        exit();
    }
    else{
        header("Location: post-piacciono.php?messaggio=Errore nell'eliminazione del post");
        exit();
    }
}

$postpiacciono->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione del post")
        $postpiacciono->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $postpiacciono->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $postpiacciono->insert_multiple("messaggio", "");

$postpiacciono->insert("post", build_liked_posts($username));
$postpiacciono->insert("post_mobile", build_liked_posts_mobile($username));

$postpiacciono->insert_multiple("suggeriti", build_lista_suggeriti());

$postpiacciono->insert("header", build_header());
$postpiacciono->insert("goback", build_goback());
$postpiacciono->insert("footer", build_footer());


echo $postpiacciono->build();