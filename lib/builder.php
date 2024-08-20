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
            "I miei dati personali" => "dati-personali.php",
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
                "I miei dati personali" => "dati-personali.php",
                "Modifica Password" => "modifica-password.php",
                "Post che mi piacciono" => "post-piacciono.php",
                "Post commentati" => "post-commentati.php",
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

        }       //TODO: mi piace e commenta devono essere sul db come contatori prima di implementare?
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
        //CAMBIO IMMAGINE IN QUESTA PAGINA??
        $mioprofilo = "<ul class=\"profilo\">
                        <li><img src = ".$row_query['profile_picture_url']." alt=\"\"/></li>  
                        <li><b>Nome: </b>".$row_query['name']."</li>
                        <li><b>Email: </b>".$row_query['email']."</li>
                        <li><b>Username: </b>".$row_query['username']."</li>
                        <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
                        <li><b>Genere: </b>".$row_query['gender']."</li>
                        <li><b>Data di nascita: </b>".$row_query['birthdate']."</li>
                        <li><b>Luogo: </b>".$row_query_profile['location']."</li>
                        <li><b>Sito Web: </b>".$row_query_profile['website']."</li>
                        <li><b>Data di iscrizione: </b>".$row_query['created_at']."</li>
                        <li><b>Ultima modifica: </b>".$row_query['updated_at']."</li>
                    </ul>";
    }

    return $mioprofilo;
}


function build_lista_amici($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM friendship WHERE username_1 = '$username' OR username_2 = '$username' AND status = 'accepted'";
    $result_query = $db->query($query);

    $lista_amici = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            if($row_query['username_1'] == $username)
                $amico = $row_query['username_2'];
            else
                $amico = $row_query['username_1'];

            $query_profile = "SELECT * FROM profile WHERE username = '$amico'";
            $result_query_profile = $db->query($query_profile);

            $query_user = "SELECT * FROM user WHERE username = '$amico'";
            $result_query_user = $db->query($query_user);

            if($result_query_user->num_rows > 0)
                $row_query_user = $result_query_user->fetch_assoc();

            if($result_query_profile->num_rows > 0)
                $row_query_profile = $result_query_profile->fetch_assoc();

            $lista_amici .= "<ul class=\"profilo\" id=\"amici\">
                                <li><img src = ".$row_query_user['profile_picture_url']." alt=\"\"/></li>  
                                <li><b>Nome: </b>".$row_query_user['name']."</li>
                                <li><b>Username: </b>".$amico."</li>
                                <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
                                <li>
                                    <fieldset>
                                        <legend>Rimuovi amicizia a ".$amico."</legend>
                                        <form method='post' action='amici.php' name='rimuovi_amicizia'> 
                                            <input type='hidden' name='amico' value='".$amico."' />
                                            <button class=\"loginbtn\" type='submit' name='submit_rimuovi_amicizia'>Rimuovi Amicizia</button>
                                        </form>
                                    </fieldset>
                                </li>
                            </ul><hr>"; //rimuovi_amicizia DA IMPLEMENTARE
    
        }
    }
    return $lista_amici;
}

function build_modifica_dati_personali($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result_query = $db->query($query);

    $modifica_dati_personali = "";

    if($result_query->num_rows > 0){
        $row_query = $result_query->fetch_assoc();

        $query_profile = "SELECT * FROM profile WHERE username = '$username'";
        $result_query_profile = $db->query($query_profile);

        if($result_query_profile->num_rows > 0)
            $row_query_profile = $result_query_profile->fetch_assoc();

        $modifica_dati_personali = "<form class='form_box' method='post' action='dati-personali.php' name='modifica_dati_personali'>
                                        <fieldset>
                                            <legend>Modifica Dati Personali</legend>
                                            <label for='name'>Nome</label>
                                            <input type='text' name='name' value='".$row_query['name']."' required>
                                            <label for='email'>Email</label>
                                            <input type='email' name='email' value='".$row_query['email']."' required>
                                            <label for='username'>Username</label>
                                            <input type='text' name='username' value='".$row_query['username']."' readonly>
                                            <label for='bio'>Biografia</label>
                                            <input type='text' name='bio' value='".$row_query_profile['bio']."' required>
                                            <label for='gender'>Genere</label>
                                            <select name='gender' required>
                                                <option value='M'".($row_query['gender']=='M'?' selected':'').">M</option>
                                                <option value='F'".($row_query['gender']=='F'?' selected':'').">F</option>
                                                <option value='Non specificato'".($row_query['gender']=='Non specificato'?' selected':'').">Non specificato</option>
                                            </select>
                                            <label for='birthdate'>Data di nascita</label>
                                            <input type='date' name='birthdate' value='".$row_query['birthdate']."' required>
                                            <label for='location'>Luogo</label>
                                            <input type='text' name='location' value='".$row_query_profile['location']."' required>
                                            <label for='website'>Sito Web</label>
                                            <input type='text' name='website' value='".$row_query_profile['website']."' required>
                                            <button class=\"loginbtn\" type='submit' name='submit_modifica_dati_personali'>Modifica</button>
                                        </fieldset>
                                    </form>";
    }

    return $modifica_dati_personali;
}

