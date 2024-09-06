<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_elimina_aula'])){
    $id_aula = $_POST['id_aula'];
    if($db->delete_aula($id_aula)){
        header("Location: aule-studio-virtuali.php?messaggio=Aula eliminata con successo");
        exit();
    }else{
        header("Location: aule-studio-virtuali.php?errore=Errore durante l'eliminazione dell'aula");
        exit();
    }
}

$aule_studio_virtuali_template = $template_engine->load_template("aule-studio-virtuali-template.html");

$aule_studio_virtuali_template->insert("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore durante l'eliminazione dell'aula"){
        $aule_studio_virtuali_template->insert("messaggio", "<div class='messaggioerrore'>".$messaggio."</div>");
    }else{
        $aule_studio_virtuali_template->insert("messaggio", "<div class='messaggio'>".$messaggio."</div>");
    }
}else
    $aule_studio_virtuali_template->insert("messaggio", "");

$aule_studio_virtuali_template->insert("filtri", build_filtri_aule());

$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

if($genre === '' && $search === ''){
    $aule_studio_virtuali_template->insert("lista_aule", build_lista_aule());
}else{
    if($search !== ''){
        $aule_studio_virtuali_template->insert("lista_aule", build_lista_aule_search($search));
    }else{
        $aule_studio_virtuali_template->insert("lista_aule", build_lista_aule_filter($genre));
    }
}

$aule_studio_virtuali_template->insert("header", build_header());
$aule_studio_virtuali_template->insert("goback", build_goback());
$aule_studio_virtuali_template->insert("footer", build_footer());


echo $aule_studio_virtuali_template->build();
