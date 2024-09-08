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


$search_template->insert_multiple("ricerca", build_search($query));

$search_template->insert("header", build_header());
$search_template->insert("goback", build_goback());
$search_template->insert("footer", build_footer());


echo $search_template->build();
