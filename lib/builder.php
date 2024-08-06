<?php

/**
 * La pagina builder aiuta nella composizione delle varie pagine
 * del sito
 */

// inclusione di globale.php
require_once("globale.php");

/**
 * Costruisce l'header della pagina
 * @return string html dell'header
 * @throws Exception se il template non è stato trovato
 */
function build_header(): string
{
    global $template_engine;

    // carico il template dell'header
    $header_template = $template_engine->load_template("header-template.html");

    // se l'utente è loggato gli do il benvenuto e la possibilità di fare il logout
    if (isset($_SESSION['Username'])) {
        $header_template ->insert("actionheader", "Benvenuto, ".$_SESSION['Username']." <a href=\"./logout.php\"><img class=\"log\" src=\"./img/logout.svg\" title=\"Esci\" alt=\"Pulsante di Logout\">Esci</a>");
    }
    else {
        $header_template ->insert("actionheader", "<a href=\"./login.php\"><img class=\"log\" src=\"./img/login.svg\" title=\"Accedi\" alt=\"Pulsante di Login\">Accedi</a>");
    }

    // restituisco il codice html dell'header
    return $header_template->build();
}

/**
 * Costruisce il footer della pagina
 * @return string html del footer
 * @throws Exception se il template non è stato trovato o fallisce la build
 */
function build_footer(): string
{
    global $template_engine;

    // carico il template del footer
    $footer_template = $template_engine->load_template("footer-template.html");

    // restituisco il codice html del footer
    return $footer_template->build();
}

/**
 * Costruisce il pulsante per tornare in cima
 * @return string html del pulsante
 * @throws Exception se il template non è stato trovato o fallisce la build
 */
function build_goback(): string
{
    global $template_engine;

    // carico il template del pulsante
    $header_template = $template_engine->load_template("goback-template.html");

    // restituisco il codice html del pulsante
    return $header_template->build();
}

function build_menu(){
    // costruzione voci del menu

    if(isset($_SESSION['Username']) && ($_SESSION['Username']=="admin")){
        $menu_items=array(
            "<span lang=\"en\">Home</span>" => "index.php",
            "Il mio profilo" => "mio-profilo.php",
            "Amici" => "amici.php",
            "Modifica dati personali" => "dati-personali.php",
            "Modifica Password" => "modifica-password.php",
            "Post che mi piacciono" => "post-piacciono.php",
            "Post commentati" => "post-commentati.php",
            "Utenti bloccati" => "utenti-bloccati.php", 
            "Post nascosti" => "post-nascosti.php",
        );
    }else{
        if(isset($_SESSION['Username'])){
            $menu_items=array(
                "<span lang=\"en\">Home</span>" => "index.php",
                "Il mio profilo" => "mio-profilo.php",
                "Amici" => "amici.php",
                "Modifica dati personali" => "dati-personali.php",
                "Modifica Password" => "modifica-password.php",
                "Post che mi piacciono" => "post-piacciono.php",
                "Post commentati" => "post-commentati.php",
            );
        }else{
            $menu_items=array(
                "<span lang=\"en\">Home</span>" => "index.php",
                "Tutti gli eventi" => "lista-eventi.php",
                "Tutti gli artisti" => "lista-artisti.php",
                "Registrati" => "registrazione.php",
            );
        }

    }

    $menu = "<ul class=\"menu\">";
    //costruisce il menu
    foreach($menu_items as $item => $link){
        if (parse_url(strtok(urldecode($_SERVER['REQUEST_URI']),'/'), PHP_URL_PATH) == $link)
            $menu .= "<li class=\"currentmenu\">$item</li>";
        else
            $menu .= "<li><a href=\"$link\">$item</a></li>";
    }
    $menu .= "</ul>";
    return $menu;
}

function build_lista_post(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_post = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $lista_post .= "<ul class=\"singolo_post\">
                                <li><a href=\"\">".$row_query['username']."</a></li>
                                <li>".$row_query['created_at']."</li>
                                <li>".$row_query['content']."</li>
                                <li>
                                    <button class=\"interact\" onclick=\"like_post(".$row_query['post_id'].")\">Mi piace</button>
                                    <button class=\"interact\" onclick=\"comment_post(".$row_query['post_id'].")\">Commenta</button>
                                </li>
                            </ul>"
        ;

        }
    }
    return $lista_post;
}

function build_mioprofilo($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result_query = $db->query($query);


    $mioprofilo = "";

    if($result_query->num_rows > 0){
        $query_profile = "SELECT * FROM profile WHERE username = '$username'";
        $result_query_profile = $db->query($query_profile);

        $row_query = $result_query->fetch_assoc();
        if($result_query_profile->num_rows > 0)
            $row_query_profile = $result_query_profile->fetch_assoc();

        $mioprofilo = "<ul class=\"profilo\">
                        <li>".$row_query['profile_picture_url']."</li>
                        <li>Nome: ".$row_query['name']."</li>
                        <li>Email: ".$row_query['email']."</li>
                        <li>Username: ".$row_query['username']."</li>
                        <li>Biografia: ".$row_query_profile['bio']."</li>
                        <li>Genere: ".$row_query['gender']."</li>
                        <li>Data di nascita: ".$row_query['birthdate']."</li>
                        <li>Luogo: ".$row_query_profile['location']."</li>
                        <li>Sito Web: ".$row_query_profile['website']."</li>
                        <li>Data di iscrizione: ".$row_query['created_at']."</li>
                        <li>Ultima modifica: ".$row_query['updated_at']."</li>
                    </ul>";
    }
}