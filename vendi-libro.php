<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_vendi_libro'])){
    $venditore = $_SESSION['Username'];
    $titolo = $_POST['titolo'];
    $autore = $_POST['autore'];
    $categoria = $_POST['categoria'];
    $anno = $_POST['anno'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    // if($_FILES['foto'])
    //     $foto = $_FILES['foto'];
    // else
    //     $foto = null;
    if($db->vendi_libro($venditore, $titolo, $autore, $categoria, $anno, $descrizione, $prezzo)){
        header("Location: compro-vendo-libri.php");
        exit();
    }
}


$vendilibro_template = $template_engine->load_template("vendi-libro-template.html");

$vendilibro_template->insert("menu", build_menu());

$vendilibro_template->insert("header", build_header());
$vendilibro_template->insert("goback", build_goback());
$vendilibro_template->insert("footer", build_footer());


echo $vendilibro_template->build();
