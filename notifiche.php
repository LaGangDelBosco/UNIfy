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
        header("Location: notifiche.php?messaggio=La notifica è stata eliminata con successo");
        exit();
    }
    else
        header("Location: notifiche.php?messaggio=Errore nell'eliminazione della notifica");
}

if(isset($_POST['submit_elimina_tutte_notifiche'])){
    $username = $_SESSION['Username'];
    if($db->elimina_tutte_notifiche($username)){
        header("Location: notifiche.php?messaggio=Tutte le notifiche sono state eliminate");
        exit();
    }
    else
        header("Location: notifiche.php?messaggio=Errore nell'eliminazione delle notifiche");
}

$notifiche_template = $template_engine->load_template("notifiche-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

$notifiche_template->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nell'eliminazione della notifica" || $messaggio == "Errore nell'eliminazione delle notifiche")
        $notifiche_template->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $notifiche_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $notifiche_template->insert_multiple("messaggio", "");

$notifiche_template->insert_multiple("lista_notifiche", build_lista_notifiche($username));

$notifiche_template->insert_multiple("search_bar", build_search_bar());

$notifiche_template->insert("header", build_header());
$notifiche_template->insert("goback", build_goback());
$notifiche_template->insert("footer", build_footer());


echo $notifiche_template->build();
