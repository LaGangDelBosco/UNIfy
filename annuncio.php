<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$tipo_annuncio = null;

$annuncio = null;

if(isset($_GET['id'])){
    $id_annuncio = $_GET['id'];
    $annuncio= $db->get_annuncio($id_annuncio);
    $tipo_annuncio= "id";
    //if(!$annuncio) TODO: da implementare (magari con pagina di errore)
}elseif(isset($_GET['myid'])){
    $id_annuncio = $_GET['myid'];
    $annuncio= $db->get_annuncio($id_annuncio);
    $tipo_annuncio= "myid";
    //if(!$annuncio) TODO: da implementare (magari con pagina di errore)
}


if(isset($_POST['submit_elimina_annuncio'])){
    $id_annuncio = $_POST['id_annuncio'];
    $db->delete_annuncio($id_annuncio);
    header("Location: compro-vendo-libri.php?messaggio=Annuncio eliminato con successo");
    exit();
}


$annuncio_template = $template_engine->load_template("annuncio-template.html");

$annuncio_template->insert("menu", build_menu());

if($annuncio!=null){
    $annuncio_template->insert_multiple("nome_libro", $annuncio['title']);
    $annuncio_template->insert("annuncio", build_annuncio($annuncio));
}

if($tipo_annuncio == "myid"){
    $annuncio_template->insert("buttons", build_buttons_mybook($id_annuncio));
    $annuncio_template->insert("tabella_interessati", build_tabella_interessati($id_annuncio));
    $annuncio_template->insert("destinatario", $annuncio['username']);
}elseif($tipo_annuncio == "id"){
    $annuncio_template->insert("buttons", build_buttons_otherbook($id_annuncio, $annuncio['username']));
    $annuncio_template->insert("tabella_interessati", "");
    $annuncio_template->insert("destinatario", $annuncio['username']);
}




$annuncio_template->insert("header", build_header());
$annuncio_template->insert("goback", build_goback());
$annuncio_template->insert("footer", build_footer());


echo $annuncio_template->build();
