<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_elimina_notifica'])){
    $id_notifica = $_POST['notification_id'];
    if($db->elimina_notifica($id_notifica)){
        header("Location: notifiche.php");    //TODO da aggiungere messaggio get
        exit();
    }
    else
        echo "Errore nell'eliminazione della notifica";
}

if(isset($_POST['submit_elimina_tutte_notifiche'])){
    $username = $_SESSION['Username'];
    if($db->elimina_tutte_notifiche($username)){
        header("Location: notifiche.php");    //TODO da aggiungere messaggio get
        exit();
    }
    else
        echo "Errore nell'eliminazione delle notifiche";
}

$notifiche_template = $template_engine->load_template("notifiche-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

$notifiche_template->insert("menu", build_menu());

$notifiche_template->insert("lista_notifiche", build_lista_notifiche($username));

$notifiche_template->insert("header", build_header());
$notifiche_template->insert("goback", build_goback());
$notifiche_template->insert("footer", build_footer());


echo $notifiche_template->build();
