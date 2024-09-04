<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}else{
    $username=$_SESSION['Username'];
}

if(isset($_POST['submit_crea_aula'])){
    $nome_aula = contrassegnaParoleInglesi($_POST['name']);
    $categoria = contrassegnaParoleInglesi($_POST['genre']);
    if($db->crea_aula($nome_aula, $categoria, $username)){
        header("Location: aule-studio-virtuali.php?messaggio=Aula creata con successo");
        exit();
    }else{
        header("Location: crea-aula.php?errore=Errore durante la creazione dell'aula");
        exit();
    }
}

$creaaula_template = $template_engine->load_template("crea-aula-template.html");

$creaaula_template->insert("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore durante la creazione dell'aula"){
        $creaaula_template->insert("messaggio", "<div id='messaggioerrore'>".$messaggio."</div>");
    }else{
        $creaaula_template->insert("messaggio", "<div id='messaggio'>".$messaggio."</div>");
    }
}else
    $creaaula_template->insert("messaggio", "");
    
$creaaula_template->insert("header", build_header());
$creaaula_template->insert("goback", build_goback());
$creaaula_template->insert("footer", build_footer());


echo $creaaula_template->build();
