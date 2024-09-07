<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}
$nome_aula = "";
if(isset($_GET['room_code'])){
    $id_aula = $_GET['room_code'];
    $aula= $db->get_aula($id_aula);
    $nome_aula = toglispan($aula['name']);
    if(!$aula) {
        header("Location: error.php?error=400?forced=1");
        exit();
    }
    /* TODO @nicmoro31: da vedere cosa fare con room_name, per me si può fare in maniera diversa,
    *  altrimenti ci tocca gestire pure gli errori per quello ed eventuali discrepanze con il room_code
    */
}


$aule_studio_virtuali_template = $template_engine->load_template("aula-template.html");

$aule_studio_virtuali_template->insert("keywords", $nome_aula);

$aule_studio_virtuali_template->insert_multiple("menu", build_menu());

$aule_studio_virtuali_template->insert_multiple("nome_aula", $nome_aula);
$aule_studio_virtuali_template->insert_multiple("nome_aula_title", toglispan($nome_aula));

$aule_studio_virtuali_template->insert_multiple("code", $id_aula);

$aule_studio_virtuali_template->insert("header", build_header());
$aule_studio_virtuali_template->insert("goback", build_goback());
$aule_studio_virtuali_template->insert("footer", build_footer());


echo $aule_studio_virtuali_template->build();
