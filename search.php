<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$search_template = $template_engine->load_template("search-template.html");

#$search_template->insert("build_keywords", build_keywords());
$search_template->insert_multiple("menu", build_menu());

function convert_youtube_links_to_iframe($text) {
    $pattern = '/(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i';
    $replacement = '<iframe width="560" height="315" src="https://www.youtube.com/embed/$4" frameborder="0" allowfullscreen></iframe>';
    return preg_replace($pattern, $replacement, $text);
}

if(isset($_GET['q'])){
    $query = $_GET['q'];
}
else{
    $query = "";
}


if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    $search_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $search_template->insert_multiple("messaggio", "");

$search_template->insert("ricerca", build_search($query));
$search_template->insert("ricerca_mobile", build_search_mobile($query));

$search_template->insert_multiple("suggeriti", build_lista_suggeriti());

$search_template->insert("header", build_header());
$search_template->insert("goback", build_goback());
$search_template->insert("footer", build_footer());


echo $search_template->build();
