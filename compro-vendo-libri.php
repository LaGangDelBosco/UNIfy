<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$comprovendolibri_template = $template_engine->load_template("compro-vendo-libri-template.html");

$comprovendolibri_template->insert("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    $comprovendolibri_template->insert("messaggio", "<div id='messaggio'>".$messaggio."</div>");
}else{
    $comprovendolibri_template->insert("messaggio", "");
}

$comprovendolibri_template->insert("filtri", build_filtri_libri());

$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($genre === '' && $author === '' && $year === '' && $search === '') {
    $comprovendolibri_template->insert("lista_libri", build_lista_libri());
} else {
    if ($search !== '') {
        $comprovendolibri_template->insert("lista_libri", build_lista_libri_search($search));
    } else {
        $comprovendolibri_template->insert("lista_libri", build_lista_libri_filter($genre, $author, $year));
    }
}

$comprovendolibri_template->insert("header", build_header());
$comprovendolibri_template->insert("goback", build_goback());
$comprovendolibri_template->insert("footer", build_footer());


echo $comprovendolibri_template->build();
