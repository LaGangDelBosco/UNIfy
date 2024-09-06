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
        $header_template ->insert("actionheader", "Benvenuto, ".$_SESSION['Username']." <a href=\"./logout.php\"><img class=\"log\" src=\"./media/logout.svg\" title=\"Esci\" alt=\"Pulsante di Logout\" />Esci</a>");
    } else {
        // altrimenti gli do la possibilità di fare il login o la registrazione
        $header_template ->insert("actionheader", "");
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
    $db = new Servizio();
    $notification_amount = $db->get_numero_notifiche($_SESSION['Username']);

    if(isset($_SESSION['Username']) && ($_SESSION['Username']=="admin")){
        $menu_items=array(
            "<span lang=\"en\">Home</span>" => "index.php",
            "Il mio profilo" => "mio-profilo.php",
            "Amici" => "amici.php",
            "Compro/Vendo Libri" => "compro-vendo-libri.php",
            "Aule Studio Virtuali" => "aule-studio-virtuali.php",
            "I miei dati personali" => "dati-personali.php",
            "Post che mi piacciono" => "post-piacciono.php",
            "Post commentati" => "post-commentati.php",
            "Utenti banditi" => "utenti-banditi.php",
            "Post nascosti" => "post-nascosti.php",
            "Notifiche ".$notification_amount => "notifiche.php",
        );
    }else{
        if(isset($_SESSION['Username'])){
            $menu_items=array(
                "<span lang=\"en\">Home</span>" => "index.php",
                "Il mio profilo" => "mio-profilo.php",
                "Amici" => "amici.php",
                "Compro/Vendo Libri" => "compro-vendo-libri.php",
                "Aule Studio Virtuali" => "aule-studio-virtuali.php",
                "I miei dati personali" => "dati-personali.php",
                "Post che mi piacciono" => "post-piacciono.php",
                "Post commentati" => "post-commentati.php",
                "Notifiche ".$notification_amount => "notifiche.php",
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

    $query = "SELECT * FROM post WHERE hidden = 0 ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_post = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $like_count = $db->get_like_count($row_query['post_id']);
            $post_id = $row_query['post_id'];
            $lista_post .= "<ul class=\"singolo_post\">";


            $lista_post .=     "<li><a href=\"profilo.php?user=".$row_query['username']."\">".$row_query['username']."</a></li>";

            if($_SESSION['Username'] == 'admin'){
                $current_page = $_SERVER['REQUEST_URI'];
                $lista_post .= "<li>
                                    <form method='post' action='index.php' name='nascondi_post'>
                                        <div>
                                            <input type='hidden' name='id_post' value='".$post_id."' />
                                            <input type='hidden' name='current_page' value='".$current_page."' />
                                            <button class=\"interact\" type='submit' name='submit_nascondi_post'>Nascondi</button>
                                        </div>
                                    </form>
                                </li>";
            }

            $lista_post .=     "<li>".$row_query['created_at']."</li>
                                <li class=\"player\">".$row_query['content']."</li>";

            if($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $lista_post .= "<li class=\"media\"><img src=" . $media_path . " alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $lista_post .= "<li class=\"media\"><video controls><source src=" . $media_path . " type=\"video/mp4\"></video></li>";
                }
            }

            $lista_post .=     "
                                <li>
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\">Mi piace</button>
                                    <span>$like_count</span>
                                </li>
                                <li>
                                        <label class=\"label_commento\" id=\"label_comment_$post_id\" for=\"comment_$post_id\">Scrivi un commento:</label>
                                        <textarea class=\"textarea_commento_index\" id='comment_$post_id' placeholder=\"Commenta\"></textarea>
                                        <button id='comment_button_$post_id' class=\"comment-interact\">Commenta</button>
                                </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if($result_query_comment->num_rows > 0){
                $lista_post .= "<li id='comment_list_". $post_id ."'><ul>";
                while($row_query_comment = $result_query_comment->fetch_assoc()){
                    $lista_post .= "<li><a href=\"profilo.php?user=".$row_query_comment['username']."\">".$row_query_comment['username']."</a></li>
                                    <li>".$row_query_comment['created_at']."</li>
                                    <li class=\"content_comm\">".$row_query_comment['content']."</li>";
                }
                $lista_post .= "</ul></li>";
            } else {
                $lista_post .= "<li id='comment_list_". $post_id ."'></li>";
            }

            $lista_post .= "</ul>";
        }
    }
    return $lista_post;
}

function build_lista_post_mobile(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE hidden = 0 ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_post = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $like_count = $db->get_like_count($row_query['post_id']);
            $post_id = $row_query['post_id'];
            $lista_post .= "<ul class=\"singolo_post\">";


            $lista_post .=     "<li><a href=\"profilo.php?user=".$row_query['username']."\">".$row_query['username']."</a></li>";

            if($_SESSION['Username'] == 'admin'){
                $current_page = $_SERVER['REQUEST_URI'];
                $lista_post .= "<li>
                                    <form method='post' action='index.php' name='nascondi_post'>
                                        <div>
                                            <input type='hidden' name='id_post' value='".$post_id."' />
                                            <input type='hidden' name='current_page' value='".$current_page."' />
                                            <button class=\"interact\" type='submit' name='submit_nascondi_post'>Nascondi</button>
                                        </div>
                                    </form>
                                </li>";
            }

            $lista_post .=     "<li>".$row_query['created_at']."</li>
                                <li class=\"player\">".$row_query['content']."</li>";

            if($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $lista_post .= "<li class=\"media\"><img src=" . $media_path . " alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $lista_post .= "<li class=\"media\"><video controls><source src=" . $media_path . " type=\"video/mp4\"></video></li>";
                }
            }

            $lista_post .=     "
                                <li>
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\">Mi piace</button>
                                    <span>$like_count</span>
                                </li>
                                <li>
                                        <label class=\"label_commento\" id=\"label_comment_mobile_$post_id\" for=\"comment_mobile_$post_id\">Scrivi un commento:</label>
                                        <textarea class=\"textarea_commento_index\" id='comment_mobile_$post_id' placeholder=\"Commenta\"></textarea>
                                        <button id='comment_button_mobile_$post_id' class=\"comment-interact\">Commenta</button>
                                </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if($result_query_comment->num_rows > 0){
                $lista_post .= "<li id='comment_list_". $post_id ."_mobile'><ul>";
                while($row_query_comment = $result_query_comment->fetch_assoc()){
                    $lista_post .= "<li><a href=\"profilo.php?user=".$row_query_comment['username']."\">".$row_query_comment['username']."</a></li>
                                    <li>".$row_query_comment['created_at']."</li>
                                    <li class=\"content_comm\">".$row_query_comment['content']."</li>";
                }
                $lista_post .= "</ul></li>";
            } else {
                $lista_post .= "<li id='comment_list_". $post_id ."_mobile'></li>";
            }

            $lista_post .= "</ul>";
        }
    }
    return $lista_post;
}

function build_datipersonali($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result_query = $db->query($query);

    $mioprofilo = "";

    if($result_query->num_rows > 0){
        $query_img = "SELECT profile_picture_path FROM profile WHERE username = '$username'";
        $result_query_img = $db->query($query_img);

        $row_query = $result_query->fetch_assoc();
        if($result_query_img->num_rows > 0)
            $row_query_img = $result_query_img->fetch_assoc();
        $mioprofilo = "<ul class=\"profilo\">
                        <li><img class='profile-picture' src = ".$row_query_img['profile_picture_path']." alt=\"\"/></li>  
                        <li><b>Nome: </b>".$row_query['name']."</li>
                        <li><b>Email: </b>".$row_query['email']."</li>
                        <li><b>Username: </b>".$row_query['username']."</li>
                        <li><b>Genere: </b>".$row_query['gender']."</li>
                        <li><b>Data di nascita: </b>".$row_query['birthdate']."</li>
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

            // $lista_amici .= "<ul class=\"profilo\" id=\"amici\">
            //                     <li><img class='profile-picture'  src = ".$row_query_user['profile_picture_path']." alt=\"\"/></li>  
            //                     <li><b>Nome: </b>".$row_query_user['name']."</li>
            //                     <li><b>Username: </b>".$amico."</li>
            //                     <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
            //                     <li>
            //                         <fieldset>
            //                             <legend>Rimuovi amicizia a ".$amico."</legend>
            //                             <form method='post' action='amici.php' name='rimuovi_amicizia'>
            //                                 <div>
            //                                     <input type='hidden' name='amico' value='".$amico."' />
            //                                 </div>
            //                                 <button class=\"loginbtn\" type='submit' name='submit_rimuovi_amicizia'>Rimuovi Amicizia</button>
            //                             </form>
            //                         </fieldset>
            //                     </li>
            //                 </ul><hr/>"; //rimuovi_amicizia DA IMPLEMENTARE

            $lista_amici .= "<div class='amico'>
                    <div class='amico-foto'>
                        <img class='profile-picture' id='friend-picture' src='".$row_query_profile['profile_picture_path']."' alt=''/>
                    </div>
                    <div class='amico-info'>
                        <ul class='profilo'>
                            <li><b>Nome: </b>".$row_query_user['name']."</li>
                            <li><b>Username: </b>".$amico."</li>
                            <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
                        </ul>
                    </div>
                    <div class='amico-azione'>
                        <fieldset>
                            <legend>Rimuovi amicizia a ".$amico."</legend>
                            <form method='post' action='amici.php' name='rimuovi_amicizia'>
                                <input type='hidden' name='amico' value='".$amico."' />
                                <button class='loginbtn' type='submit' name='submit_rimuovi_amicizia'>Rimuovi Amicizia</button>
                            </form>
                        </fieldset>
                    </div>
                </div><hr/>";
    
        }
    }
    else
        $lista_amici = "<p id=\"messaggio\">Non hai amici</p>";
    return $lista_amici;
}

function build_lista_amici_mobile($username){
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


            $lista_amici .= "<div class='amico'>
                    <div class='amico-foto'>
                        <img class='profile-picture' id='friend-picture_mobile' src='".$row_query_profile['profile_picture_path']."' alt=''/>
                    </div>
                    <div class='amico-info'>
                        <ul class='profilo'>
                            <li><b>Nome: </b>".$row_query_user['name']."</li>
                            <li><b>Username: </b>".$amico."</li>
                            <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
                        </ul>
                    </div>
                    <div class='amico-azione'>
                        <fieldset>
                            <legend>Rimuovi amicizia a ".$amico."</legend>
                            <form method='post' action='amici.php' name='rimuovi_amicizia'>
                                <input type='hidden' name='amico' value='".$amico."' />
                                <button class='loginbtn' type='submit' name='submit_rimuovi_amicizia'>Rimuovi Amicizia</button>
                            </form>
                        </fieldset>
                    </div>
                </div><hr/>";
    
        }
    }
    else
        $lista_amici = "<p id=\"messaggio\">Non hai amici</p>";
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
                                        <div>
                                            <label for='name'>Nome</label><br>
                                            <input type='text' id='name' name='name' value='".$row_query['name']."' required /><br/>
                                            <label for='email'>Email</label><br/>
                                            <input type='email' id='email' name='email' value='".$row_query['email']."' required /><br/>
                                            <label for='username'>Username</label><br/>
                                            <input type='text' id='username' name='username' value='".$row_query['username']."' readonly /><br/>
                                            <label for='gender'>Genere</label><br/>
                                            <select id='gender' name='gender' required>
                                                <option value='M'".($row_query['gender']=='M'?' selected':'').">M</option>
                                                <option value='F'".($row_query['gender']=='F'?' selected':'').">F</option>
                                                <option value='Non specificato'".($row_query['gender']=='Non specificato'?' selected':'').">Non specificato</option>
                                            </select><br/>
                                            <label for='birthdate'>Data di nascita</label><br/>
                                            <input type='date' id='birthdate' name='birthdate' value='".$row_query['birthdate']."' required /><br/>
                                            <fieldset>
                                                <legend>Bottone Modifica Dati Personali</legend>
                                                <button class=\"loginbtn\" type='submit' name='submit_modifica_dati_personali'>Modifica</button>
                                            </fieldset>
                                        </div>
                                    </form>";
    }

    return $modifica_dati_personali;
}



function build_modifica_profilo($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM profile WHERE username = '$username'";
    $result_query = $db->query($query);

    $modifica_profilo = "";

    if($result_query->num_rows > 0){
        $row_query = $result_query->fetch_assoc();

        $modifica_profilo = "<form class='form_box' method='post' action='mio-profilo.php' name='modifica_profilo' enctype='multipart/form-data'>
                                <div>
                                    <label for='bio'>Biografia</label><br>
                                    <textarea id='bio' name='bio' required>".cambialospan($row_query['bio'])."</textarea><br/>
                                    <label for='location'>Luogo</label><br/>
                                    <input type='text' id='location' name='location' value='".$row_query['location']."' required /><br/>
                                    <label for='website'>Sito Web</label><br/>
                                    <input type='text' id='website' name='website' value='".$row_query['website']."' required /><br/>
                                    <label for='profile_picture_path'>Foto Profilo</label><br/>
                                    <input type='file' id='profile_picture_path' name='profile_picture_path' accept='image/*' /><br/>
                                    <fieldset>
                                        <legend>Bottone Modifica Profilo</legend>
                                        <button class=\"loginbtn\" type='submit' name='submit_modifica_profilo'>Modifica</button>
                                    </fieldset>
                                </div>
                            </form>";
    }

    return $modifica_profilo;
}

function build_modifica_profilo_mobile($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM profile WHERE username = '$username'";
    $result_query = $db->query($query);

    $modifica_profilo = "";

    if($result_query->num_rows > 0){
        $row_query = $result_query->fetch_assoc();

        $modifica_profilo = "<form class='form_box' method='post' action='mio-profilo.php' name='modifica_profilo' enctype='multipart/form-data'>
                                <div>
                                    <label for='bio_mobile'>Biografia</label><br>
                                    <textarea id='bio_mobile' name='bio' required>".cambialospan($row_query['bio'])."</textarea><br/>
                                    <label for='location_mobile'>Luogo</label><br/>
                                    <input type='text' id='location_mobile' name='location' value='".$row_query['location']."' required /><br/>
                                    <label for='website_mobile'>Sito Web</label><br/>
                                    <input type='text' id='website_mobile' name='website' value='".$row_query['website']."' required /><br/>
                                    <label for='profile_picture_path_mobile'>Foto Profilo</label><br/>
                                    <input type='file' id='profile_picture_path_mobile' name='profile_picture_path' accept='image/*' /><br/>
                                    <fieldset>
                                        <legend>Bottone Modifica Profilo</legend>
                                        <button class=\"loginbtn\" type='submit' name='submit_modifica_profilo'>Modifica</button>
                                    </fieldset>
                                </div>
                            </form>";
    }

    return $modifica_profilo;
}

function build_mypost($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE username = '$username' AND hidden = 0 ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $mypost = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $post_id = $row_query['post_id'];
            $like_count = $db->get_like_count($post_id);

            $mypost .= "<ul class=\"singolo_post\" id=$post_id>
                            <li><a href=\"\">".$row_query['username']."</a></li>";

            if($_SESSION['Username'] == 'admin'){
                $current_page = $_SERVER['REQUEST_URI'];
                $mypost .= "<li>
                                <form method='post' action='mio-profilo.php' name='nascondi_post'>
                                    <div>
                                        <input type='hidden' name='id_post' value='".$post_id."' />
                                        <input type='hidden' name='current_page' value='".$current_page."' />
                                        <button class=\"interact\" type='submit' name='submit_nascondi_post'>Nascondi</button>
                                    </div>
                                </form>
                            </li>";
            }

            $mypost .= "<li>".$row_query['created_at']."</li>
                            <li class=\"player\">".$row_query['content']."</li>";

            if($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $mypost .= "<li class=\"media\"><img src=" . $media_path . " alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $mypost .= "<li class=\"media\"><video controls><source src=" . $media_path . " type=\"video/mp4\"></video></li>";
                }
            }

             $mypost .= "<li class=\"post-actions\">
                                <fieldset>
                                    <legend>Interazioni post del ". $row_query['created_at'] ." </legend>
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\">Commenta</button>";


            if($_SESSION['Username'] == $username) {
                $mypost .= "            <form method='post' action='mio-profilo.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post'>Elimina</button>
                                        </div>
                                    </form>";
            }

            $mypost .= "        </fieldset>
                            </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if($result_query_comment->num_rows > 0){
                $mypost .= "<li id='comment_list_". $post_id ."'><ul>";
                while($row_query_comment = $result_query_comment->fetch_assoc()){
                    $mypost .= "<li><a href=\"profilo.php?user=".$row_query_comment['username']."\">".$row_query_comment['username']."</a></li>
                                    <li>".$row_query_comment['created_at']."</li>
                                    <li class=\"content_comm\">".$row_query_comment['content']."</li>";
                }
                $mypost .= "</ul></li>";
            } else {
                $mypost .= "<li id='comment_list_". $post_id ."'></li>";
            }

            $mypost .= "</ul>";
        }
    }

    return $mypost;
}

function build_lista_notifiche($username){
    $db = new Servizio();
    $db->apriconn();

    $query = "SELECT * FROM notification WHERE receiver_username = '$username' ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $notifiche = "";

    if($result_query->num_rows > 0){

        $notifiche = "<form method='post' action='notifiche.php' name='elimina_tutte_notifiche'>
                        <div>
                            <button class=\"interact\" type='submit' name='submit_elimina_tutte_notifiche'>Elimina tutte le notifiche</button>
                        </div>
                    </form>";

        while($row_query = $result_query->fetch_assoc()){
            $notifiche .= "<ul class=\"singola_notifica\">
                            <li><a href=\"profilo.php?user=".$row_query['sender_username']."\">".$row_query['sender_username']."</a></li>
                            <li>".$row_query['created_at']."</li>";
            $notifiche .= "<li>".$row_query['content']."</li>
                            <li>
                                <form method='post' action='notifiche.php' name='elimina_notifica'>
                                    <div>
                                        <input type='hidden' name='notification_id' value='".$row_query['notification_id']."' />
                                        <button class=\"interact\" type='submit' name='submit_elimina_notifica'>Elimina</button>
                                    </div>
                                </form>
                            </li>
                        </ul>";
        }
    } else {
        $notifiche .= "<p>Non è presente alcuna notifica</p>";
    }

    return $notifiche;
}

function build_mypost_mobile($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE username = '$username' AND hidden = 0 ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $mypost = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $post_id = $row_query['post_id'];
            $like_count = $db->get_like_count($post_id);

            $mypost .= "<ul class=\"singolo_post\">
                            <li><a href=\"\">".$row_query['username']."</a></li>";

            if($_SESSION['Username'] == 'admin'){
                $current_page = $_SERVER['REQUEST_URI'];
                $mypost .= "<li>
                                <form method='post' action='mio-profilo.php' name='nascondi_post'>
                                    <div>
                                        <input type='hidden' name='id_post' value='".$post_id."' />
                                        <input type='hidden' name='current_page' value='".$current_page."' />
                                        <button class=\"interact\" type='submit' name='submit_nascondi_post'>Nascondi</button>
                                    </div>
                                </form>
                            </li>";
            }

            $mypost .= "<li>".$row_query['created_at']."</li>
                            <li class=\"player\">".$row_query['content']."</li>";

            if($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $mypost .= "<li class=\"media\"><img src=" . $media_path . " alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $mypost .= "<li class=\"media\"><video controls><source src=" . $media_path . " type=\"video/mp4\"></video></li>";
                }
            }

             $mypost .= "<li class=\"post-actions\">
                                <fieldset>
                                    <legend>Interazioni post del ". $row_query['created_at'] ." </legend>
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_mobile_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_mobile_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_mobile_$post_id' class=\"comment-interact\">Commenta</button>";


            if($_SESSION['Username'] == $username) {
                $mypost .= "            <form method='post' action='mio-profilo.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post'>Elimina</button>
                                        </div>
                                    </form>";
            }

            $mypost .= "        </fieldset>
                            </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if($result_query_comment->num_rows > 0){
                $mypost .= "<li id='comment_list_". $post_id ."_mobile'><ul>";
                while($row_query_comment = $result_query_comment->fetch_assoc()){
                    $mypost .= "<li><a href=\"profilo.php?user=".$row_query_comment['username']."\">".$row_query_comment['username']."</a></li>
                                    <li>".$row_query_comment['created_at']."</li>
                                    <li class=\"content_comm\">".$row_query_comment['content']."</li>";
                }
                $mypost .= "</ul></li>";
            } else {
                $mypost .= "<li id='comment_list_". $post_id ."_mobile'></li>";
            }

            $mypost .= "</ul>";
        }
    }

    return $mypost;
}

function build_lista_libri(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM book ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_libri = "";

    if($result_query->num_rows > 0){
        $lista_libri = "<div class=\"product-list\">";
        while($row_query = $result_query->fetch_assoc()){
            $lista_libri .= "<ul class=\"product-card\">
                                    <li class=\"product-image\">
                                        <img src=".$row_query['cover_path']." alt=\"\" />
                                    </li>
                                    <li class=\"product-info\">
                                        <h3>".$row_query['title']. "</h3>
                                        <p><b>Autore: </b>".$row_query['author']. "</p>
                                        <p><b>Categoria: </b>".$row_query['genre']. "</p>
                                        <p><b>Anno di pubblicazione: </b>".$row_query['year']. "</p>
                                        <p><b>Venditore: </b>".$row_query['username']. "</p>
                                        <p><b>Prezzo: </b> €".$row_query['price']. "</p>";

            if(isset($_SESSION['Username']) && $_SESSION['Username'] == $row_query['username']){
                $lista_libri .= "<form method='get' action='annuncio.php?myid=".$row_query['book_id']."' name='vedi_annuncio'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."' aria-label=\"Vedi il tuo annuncio del libro ".togliSpan($row_query['title'])."\" >Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."' aria-label=\"Vedi annuncio del libro ".togliSpan($row_query['title'])."\">Vedi annuncio</button>
                                    </div>
                                </form>";
            }
            $lista_libri .= "</li>
                                </ul>";
        }
        $lista_libri .= "</div>";
    }else
        $lista_libri .= "<p class=\"msg_centrato\">Non ci sono libri in vendita</p>";

    return $lista_libri;
}


function build_annuncio($annuncio){
    $db = new Servizio;
    $db->apriconn();

    $annuncio = "<ul class='annuncio_container'>
                    <li class='img_annuncio'>
                        <img src=".$annuncio['cover_path']." alt=\"\"/>
                    </li>
                    <ul class='dati_annuncio'>
                        <li><b>Autore: </b>".$annuncio['author']."</li>
                        <li><b>Categoria: </b>".$annuncio['genre']."</li>
                        <li><b>Anno di pubblicazione: </b>".$annuncio['year']."</li>
                        <li><b>Descrizione: </b>".$annuncio['description']."</li>
                        <li><b>Venditore: </b>".$annuncio['username']."</li>
                        <li><b>Prezzo: </b> €".$annuncio['price']."</li>
                    </ul>
                </ul>";
    
    return $annuncio;

}


function build_buttons_mybook($id_annuncio){
    $buttons="<form method='post' action='annuncio.php?myid=".$id_annuncio."' name='form_elimina_annuncio' >
                <div>
                    <input type='hidden' name='id_annuncio' value='".$id_annuncio."'>
                    <button id=\"annuncio_button\" class=\"deletebtn\" type=\"submit\" name=\"submit_elimina_annuncio\">Elimina annuncio</button>
                </div>
                <hr/>
            </form>";
    return $buttons;
}

function build_buttons_otherbook($id_annuncio, $username){
    $buttons = "<form id='contactForm_".$id_annuncio."' onsubmit='openChat(".$id_annuncio.", \"".$username."\"); return false;'>
                    <div>
                        <button id=\"annuncio_button\" class=\"loginbtn\" type=\"submit\">Contatta il venditore</button>
                    </div>
                </form>";
    return $buttons;
}

function build_tabella_interessati($id_annuncio){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT sender_username FROM chat_message WHERE id_annuncio = $id_annuncio AND sender_username != '".$_SESSION['Username']."'";
    $result_query = $db->query($query);

    $tabella_interessati = "<h3> Interessati </h3>";

    if($result_query->num_rows > 0){
        $tabella_interessati .= "<table class=\"interessati\" aria-describedby=\"descrizione_tab_interessati\">
        <p id=\"descrizione_tab_interessati\">Questa tabella mostra gli utenti interessati all'annuncio. La prima colonna mostra il nome utente dell'interessato, la seconda colonna contiene un pulsante per aprire la chat con l'utente interessato.</p>
        <thead>
            <tr>
                <th scope=\"col\">Username</th>
                <th scope=\"col\">Azioni</th>
            </tr>
        </thead>
        <tbody>";
        while ($contact = $result_query->fetch_assoc()) {
        $tabella_interessati .= "<tr>
                            <td>".$contact['sender_username']."</td>
                            <td>
                                <button class=\"loginbtn\" onclick='openChat(".$id_annuncio.", \"".$contact['sender_username']."\")' aria-label=\"Bottone per aprire la chat con l'utente ".$contact['sender_username']."\">Apri chat</button>
                            </td>
                        </tr>";
        }
        $tabella_interessati .= "</tbody></table>";
    }
    else
        $tabella_interessati .= "<p class=\"msg_centrato\">Non ci sono interessati</p>";

    return $tabella_interessati;
}

function build_tabella_interessati_mobile($id_annuncio){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT sender_username FROM chat_message WHERE id_annuncio = $id_annuncio AND sender_username != '".$_SESSION['Username']."'";
    $result_query = $db->query($query);

    $tabella_interessati = "<h3> Interessati </h3>";

    if($result_query->num_rows > 0){
        $tabella_interessati .= "<table class=\"interessati\" aria-describedby=\"descrizione_tab_interessati_mobile\">
        <p id=\"descrizione_tab_interessati_mobile\">Questa tabella mostra gli utenti interessati all'annuncio. La prima colonna mostra il nome utente dell'interessato, la seconda colonna contiene un pulsante per aprire la chat con l'utente interessato.</p>
        <thead>
            <tr>
                <th scope=\"col\">Username</th>
                <th scope=\"col\">Azioni</th>
            </tr>
        </thead>
        <tbody>";
        while ($contact = $result_query->fetch_assoc()) {
        $tabella_interessati .= "<tr>
                            <td>".$contact['sender_username']."</td>
                            <td>
                                <button class=\"loginbtn\" onclick='openChat(".$id_annuncio.", \"".$contact['sender_username']."\")' aria-label=\"Bottone per aprire la chat con l'utente ".$contact['sender_username']."\">Apri chat</button>
                            </td>
                        </tr>";
        }
        $tabella_interessati .= "</tbody></table>";
    }
    else
        $tabella_interessati .= "<p class=\"msg_centrato\">Non ci sono interessati</p>";

    return $tabella_interessati;
}

function build_filtri_libri(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT genre FROM book ORDER BY genre ASC";
    $result_query = $db->query($query);

    $query2 = "SELECT DISTINCT author FROM book ORDER BY author ASC";
    $result_query2 = $db->query($query2);

    $query3 = "SELECT DISTINCT year FROM book ORDER BY year DESC";
    $result_query3 = $db->query($query3);

    $genres = [];
    $authors = [];
    $years = [];

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            if (!in_array($row_query['genre'], $genres)) {
                $genres[] = $row_query['genre'];
            }
        }
    }

    if($result_query2->num_rows > 0){
        while($row_query = $result_query2->fetch_assoc()){
            if (!in_array($row_query['author'], $authors)) {
                $authors[] = $row_query['author'];
            }
        }
    }

    if($result_query3->num_rows > 0){
        while($row_query = $result_query3->fetch_assoc()){
            if (!in_array($row_query['year'], $years)) {
                $years[] = $row_query['year'];
            }
        }
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $selected_genre = isset($_GET['genre']) ? $_GET['genre'] : '';
    $selected_author = isset($_GET['author']) ? $_GET['author'] : '';
    $selected_year = isset($_GET['year']) ? $_GET['year'] : '';

    $filtri_libri = "<form method='get' class='form_filtri' action='compro-vendo-libri.php' name='filtri_libri'>
                        <div>
                            <label for='genre'>Genere: </label>
                            <select id='genre' name='genre'>
                                <option value=''>Tutti</option>";
    foreach ($genres as $genre) {
        $selected = $genre == $selected_genre ? "selected" : "";
        $filtri_libri .= "<option value='".$genre."' ".$selected.">".$genre."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <label for='author'>Autore: </label>
                        <select id='author' name='author'>
                            <option value=''>Tutti</option>";
    foreach ($authors as $author) {
        $selected = $author == $selected_author ? "selected" : "";
        $filtri_libri .= "<option value='".$author."' ".$selected.">".$author."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <label for='year'>Anno di pubblicazione: </label>
                        <select id='year' name='year'>
                            <option value=''>Tutti</option>";
    foreach ($years as $year) {
        $selected = $year == $selected_year ? "selected" : "";
        $filtri_libri .= "<option value='".$year."' ".$selected.">".$year."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <button class='interact' type='submit' aria-label='Bottone di ricerca per filtri'>Filtra</button>
                    </div>
                    
                </form>";

                $filtri_libri .= "<form method='get' class='form_search' action='compro-vendo-libri.php' name='search_libri'>
                                    <div>
                                        <label for='search'>Cerca: </label>
                                        <input type='text' id='search' name='search' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                                        <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                                    </div> 
                                </form>";

    return $filtri_libri;
}

function build_filtri_libri_mobile(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT genre FROM book ORDER BY genre ASC";
    $result_query = $db->query($query);

    $query2 = "SELECT DISTINCT author FROM book ORDER BY author ASC";
    $result_query2 = $db->query($query2);

    $query3 = "SELECT DISTINCT year FROM book ORDER BY year DESC";
    $result_query3 = $db->query($query3);

    $genres = [];
    $authors = [];
    $years = [];

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            if (!in_array($row_query['genre'], $genres)) {
                $genres[] = $row_query['genre'];
            }
        }
    }

    if($result_query2->num_rows > 0){
        while($row_query = $result_query2->fetch_assoc()){
            if (!in_array($row_query['author'], $authors)) {
                $authors[] = $row_query['author'];
            }
        }
    }

    if($result_query3->num_rows > 0){
        while($row_query = $result_query3->fetch_assoc()){
            if (!in_array($row_query['year'], $years)) {
                $years[] = $row_query['year'];
            }
        }
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $selected_genre = isset($_GET['genre']) ? $_GET['genre'] : '';
    $selected_author = isset($_GET['author']) ? $_GET['author'] : '';
    $selected_year = isset($_GET['year']) ? $_GET['year'] : '';

    $filtri_libri = "<form method='get' class='form_filtri' action='compro-vendo-libri.php' name='filtri_libri'>
                        <div>
                            <label for='genre_mobile'>Genere: </label>
                            <select id='genre_mobile' name='genre'>
                                <option value=''>Tutti</option>";
    foreach ($genres as $genre) {
        $selected = $genre == $selected_genre ? "selected" : "";
        $filtri_libri .= "<option value='".$genre."' ".$selected.">".$genre."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <label for='author_mobile'>Autore: </label>
                        <select id='author_mobile' name='author'>
                            <option value=''>Tutti</option>";
    foreach ($authors as $author) {
        $selected = $author == $selected_author ? "selected" : "";
        $filtri_libri .= "<option value='".$author."' ".$selected.">".$author."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <label for='year_mobile'>Anno di pubblicazione: </label>
                        <select id='year_mobile' name='year'>
                            <option value=''>Tutti</option>";
    foreach ($years as $year) {
        $selected = $year == $selected_year ? "selected" : "";
        $filtri_libri .= "<option value='".$year."' ".$selected.">".$year."</option>";
    }
    $filtri_libri .= "</select>
                    </div>
                    <div>
                        <button class='interact' type='submit' aria-label='Bottone di ricerca per filtri'>Filtra</button>
                    </div>
                    
                </form>";

                $filtri_libri .= "<form method='get' class='form_search' action='compro-vendo-libri.php' name='search_libri'>
                                    <div>
                                        <label for='search_mobile'>Cerca: </label>
                                        <input type='text' id='search_mobile' name='search' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                                        <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                                    </div> 
                                </form>";

    return $filtri_libri;
}

function build_lista_libri_filter($genere, $autore, $anno){
    $db = new Servizio;
    $db->apriconn();

        // Inizio la query di base
        $query = "SELECT * FROM book WHERE 1=1";

        // Aggiungo condizioni dinamiche in base ai filtri selezionati
        if (!empty($genere)) {
            $query .= " AND LOWER(genre) = LOWER('$genere')";
        }
        if (!empty($autore)) {
            $query .= " AND LOWER(author) = LOWER('$autore')";
        }
        if (!empty($anno)) {
            $query .= " AND year = '$anno'";
        }
    
        // Ordino i risultati
        $query .= " ORDER BY created_at DESC";

    $result_query = $db->query($query);

    $lista_libri = "";

    if($result_query->num_rows > 0){
        $lista_libri = "<div class=\"product-list\">";
        while($row_query = $result_query->fetch_assoc()){
            $lista_libri .= "<ul class=\"product-card\">
                                    <li class=\"product-image\">
                                        <img src=".$row_query['cover_path']." alt=\"Libro in vendita\" />
                                    </li>
                                    <li class=\"product-info\">
                                        <h3>".$row_query['title']. "</h3>
                                        <p><b>Autore: </b>".$row_query['author']. "</p>
                                        <p><b>Categoria: </b>".$row_query['genre']. "</p>
                                        <p><b>Anno di pubblicazione: </b>".$row_query['year']. "</p>
                                        <p><b>Venditore: </b>".$row_query['username']. "</p>
                                        <p><b>Prezzo: </b> €".$row_query['price']. "</p>";

            if(isset($_SESSION['Username']) && $_SESSION['Username'] == $row_query['username']){
                $lista_libri .= "<form method='get' action='annuncio.php?myid=".$row_query['book_id']."' name='vedi_annuncio'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."'>Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."'>Vedi annuncio</button>
                                    </div>
                                </form>";
            }
            $lista_libri .= "</li>
                                </ul>";
        }
        $lista_libri .= "</div>";
    }else
        $lista_libri .= "<p class=\"msg_centrato\">Non ci sono libri in vendita per i filtri selezionati</p>";

    return $lista_libri;
}

function build_lista_libri_search($stringa){
    $db = new Servizio;
    $db->apriconn();

    $stringa = strtolower($stringa);

    $query = "SELECT * FROM book WHERE LOWER(title) LIKE '%$stringa%' OR LOWER(author) LIKE '%$stringa%' OR LOWER(genre) LIKE '%$stringa%' ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_libri = "";

    if($result_query->num_rows > 0){
        $lista_libri = "<div class=\"product-list\">";
        while($row_query = $result_query->fetch_assoc()){
            $lista_libri .= "<ul class=\"product-card\">
                                    <li class=\"product-image\">
                                        <img src=".$row_query['cover_path']." alt=\"Libro in vendita\" />
                                    </li>
                                    <li class=\"product-info\">
                                        <h3>".$row_query['title']. "</h3>
                                        <p><b>Autore: </b>".$row_query['author']. "</p>
                                        <p><b>Categoria: </b>".$row_query['genre']. "</p>
                                        <p><b>Anno di pubblicazione: </b>".$row_query['year']. "</p>
                                        <p><b>Venditore: </b>".$row_query['username']. "</p>
                                        <p><b>Prezzo: </b> €".$row_query['price']. "</p>";

            if(isset($_SESSION['Username']) && $_SESSION['Username'] == $row_query['username']){
                $lista_libri .= "<form method='get' action='annuncio.php?myid=".$row_query['book_id']."' name='vedi_annuncio'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."'>Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."'>Vedi annuncio</button>
                                    </div>
                                </form>";
            }
            $lista_libri .= "</li>
                                </ul>";
        }
        $lista_libri .= "</div>";
    }else
        $lista_libri .= "<p class=\"msg_centrato\">Non ci sono libri in vendita per la ricerca effettuata</p>";

    return $lista_libri;
}function build_liked_posts($username)
{
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE post_id IN (SELECT post_id FROM likes WHERE username = '$username') ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $liked_posts = "";

    if ($result_query->num_rows > 0) {
        while ($row_query = $result_query->fetch_assoc()) {
            $post_id = $row_query['post_id'];
            $post_username = $row_query['username'];
            $like_count = $db->get_like_count($post_id);

            $liked_posts .= "<ul class=\"singolo_post\">
                            <li><a href=\"profilo.php?user=$post_username\">$post_username</a></li>
                            <li>$row_query[created_at]</li>
                            <li>$row_query[content]</li>";

            if ($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $liked_posts .= "<li class=\"media\"><img src=$media_path alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $liked_posts .= "<li class=\"media\"><video controls><source src=$media_path type=\"video/mp4\"></video></li>";
                }
            }

            $liked_posts .= "<li class=\"post-actions\">
                                <fieldset>
                                    <legend>Interazioni post del $row_query[created_at]</legend>
                                    <button class=\"like-interact\" data-post-id=\"$post_id\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\">Commenta</button>";

            if($_SESSION['Username'] == $post_username) {
                $liked_posts .= "            <form method='post' action='post-piacciono.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post'>Elimina</button>
                                        </div>
                                    </form>
                                    </fieldset>
                                    </li>";
            }else{
                $liked_posts .= "</fieldset></li>";
            }

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $liked_posts .= "<li id='comment_list_$post_id'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $liked_posts .= "<li><a href=\"profilo.php?user=$row_query_comment[username]\">$row_query_comment[username]</a></li>
                                    <li>$row_query_comment[created_at]</li>
                                    <li class=\"content_comm\">$row_query_comment[content]</li>";
                }
                $liked_posts .= "</ul></li>";
            } else {
                $liked_posts .= "<li id='comment_list_$post_id'></li>";
            }

            $liked_posts .= "</ul>";
        }
    }

    return $liked_posts;
}

function build_commented_posts($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE post_id IN (SELECT post_id FROM comment WHERE username = '$username') ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $commented_posts = "";

    if($result_query->num_rows > 0) {
        while ($row_query = $result_query->fetch_assoc()) {
            $post_id = $row_query['post_id'];
            $post_username = $row_query['username'];
            $like_count = $db->get_like_count($post_id);

            $commented_posts .= "<ul class=\"singolo_post\">
                            <li><a href=\"profilo.php?user=$post_username\">$post_username</a></li>
                            <li>$row_query[created_at]</li>
                            <li>$row_query[content]</li>";

            if ($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $commented_posts .= "<li class=\"media\"><img src=$media_path alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $commented_posts .= "<li class=\"media\"><video controls><source src=$media_path type=\"video/mp4\"></video></li>";
                }
            }

            $commented_posts .= "<li class=\"post-actions\">
                                <fieldset>
                                    <legend>Interazioni post del $row_query[created_at]</legend>
                                    <button class=\"like-interact\" data-post-id=\"$post_id\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\">Commenta</button>";

            if ($_SESSION['Username'] == $post_username) {
                $commented_posts .= "            <form method='post' action='post-commentati.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post'>Elimina</button>
                                        </div>
                                    </form>
                                    </fieldset>
                                    </li>";
            }else{
                $commented_posts .= "</fieldset></li>";
            }

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $commented_posts .= "<li id='comment_list_$post_id'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $commented_posts .= "<li><a href=\"profilo.php?user=$row_query_comment[username]\">$row_query_comment[username]</a></li>
                                    <li>$row_query_comment[created_at]</li>
                                    <li class=\"content_comm\">$row_query_comment[content]</li>";
                }
                $commented_posts .= "</ul></li>";
            } else {
                $commented_posts .= "<li id='comment_list_$post_id'></li>";
            }

            $commented_posts .= "</ul>";
        }
    }

    return $commented_posts;
}function build_lista_utenti_banditi(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM user WHERE banned = 1";
    $result_query = $db->query($query);

    $lista_utenti_banditi = "";

    if($result_query->num_rows > 0) {
        while ($row_query = $result_query->fetch_assoc()) {
            $lista_utenti_banditi .= "<ul class=\"profilo\">
                                        <li><img class='profile-picture' src = " . $row_query['profile_picture_path'] . " alt=\"\"/></li>  
                                        <li><b>Nome: </b>" . $row_query['name'] . "</li>
                                        <li><b>Email: </b>" . $row_query['email'] . "</li>
                                        <li><b>Username: </b>" . $row_query['username'] . "</li>
                                        <li><b>Motivo del ban: </b>" . $row_query['ban_reason'] . "</li>
                                        <li><b>Data del ban: </b>" . $row_query['ban_start'] . "</li>
                                        <li>
                                            <fieldset>
                                                <legend>Rimuovi ban a " . $row_query['username'] . "</legend>
                                                <form method='post' action='utenti-banditi.php' name='rimuovi_ban'>
                                                    <div>
                                                        <input type='hidden' name='username' value='" . $row_query['username'] . "' />
                                                    </div>
                                                    <button class=\"loginbtn\" type='submit' name='submit_rimuovi_ban'>Rimuovi Ban</button>
                                                </form>
                                            </fieldset>
                                        </li>
                                    </ul><hr/>";
        }
    }

    return $lista_utenti_banditi;
}

function build_post_nascosti(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE hidden = 1";
    $result_query = $db->query($query);

    $post_nascosti = "";

    if($result_query->num_rows > 0) {
        while ($row_query = $result_query->fetch_assoc()) {
            $post_id = $row_query['post_id'];
            $like_count = $db->get_like_count($post_id);

            $post_nascosti .= "<ul class=\"singolo_post\">
                            <li><a href=\"profilo.php?user=" . $row_query['username'] . "\">" . $row_query['username'] . "</a></li>";


            $post_nascosti .= "<li>
                                    <form method='post' action='post-nascosti.php' name='mostra_post'>
                                        <div>
                                            <input type='hidden' name='id_post' value='" . $post_id . "' />
                                            <button class=\"interact\" type='submit' name='submit_mostra_post'>Mostra</button>
                                        </div>
                                    </form>
                                </li>";

            $post_nascosti .= "<li>" . $row_query['created_at'] . "</li>
                            <li>" . $row_query['content'] . "</li>";

            if ($db->get_media_path($post_id) != "NULL") {
                // controlla se il media è un'immagine o un video
                $media_path = $db->get_media_path($post_id);
                $media_type = $db->get_media_type($post_id);
                if ($media_type == "image") {
                    $post_nascosti .= "<li class=\"media\"><img src=" . $media_path . " alt=\"\"/></li>";
                } else if ($media_type == "video") {
                    $post_nascosti .= "<li class=\"media\"><video controls><source src=" . $media_path . " type=\"video/mp4\"></video></li>";
                }
            }

            $post_nascosti .= "<li class=\"post-actions\">
                                <fieldset>
                                    <legend>Interazioni post del " . $row_query['created_at'] . " </legend>
                                    <button class=\"like-interact\" data-post-id=\"" . $post_id . "\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\">Commenta</button>
                                </fieldset>
                            </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $post_nascosti .= "<li id='comment_list_" . $post_id . "'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $post_nascosti .= "<li><a href=\"profilo.php?user=" . $row_query_comment['username'] . "\">" . $row_query_comment['username'] . "</a></li>
                                    <li>" . $row_query_comment['created_at'] . "</li>
                                    <li class=\"content_comm\">" . $row_query_comment['content'] . "</li>";
                }
                $post_nascosti .= "</ul></li>";
            } else {
                $post_nascosti .= "<li id='comment_list_" . $post_id . "'></li>";
            }
            $post_nascosti .= "</ul>";
        }
    }

    return $post_nascosti;
}

function build_lista_aule(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM room ORDER BY name ASC";
    $result_query = $db->query($query);

    $lista_aule = "";

    if($result_query->num_rows > 0) {
        $lista_aule = "<div class=\"product-list\">";
        while ($row_query = $result_query->fetch_assoc()) {
            $lista_aule .= "<ul class=\"product-card\">
                                    <li class=\"product-info\">
                                        <h3>" . $row_query['name'] . "</h3>
                                        <p><b>Categoria: </b>" . $row_query['genre'] . "</p>
                                        <p><b>Creata il: </b>" . $row_query['created_at'] . "</p>
                                        <p><b>Creata da: </b>" . $row_query['created_by'] . "</p>
                                    </li>";
            if($_SESSION['Username'] == $row_query['created_by']){
                $lista_aule .= "<form method='post' action='aule-studio-virtuali.php' name='elimina_aula'>
                                    <div>
                                        <input type='hidden' name='id_aula' value='".$row_query['id']."' />
                                        <button class=\"deletebtn\" type='submit' name='submit_elimina_aula' aria-label='Elimina aula di \"".$row_query['name']."\"'>Elimina</button>
                                    </div>
                                </form>";
            }
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\"&room_name=urlencode(\"".toglispan($row_query['name'])."\")' name='vedi_aula'>
                                    <div>
                                        <input type='hidden' name='room_name' value='".urlencode(toglispan($row_query['name']))."' />
                                        <button class=\"loginbtn\" type='submit' name='room_code' value='".$row_query['id']."' aria-label='Entra in aula di \"".$row_query['name']."\"'>Entra in aula</button>
                                    </div>
                                </form>
                            </ul>";

        }
        $lista_aule .= "</div>";
    }else
        $lista_aule .= "<p class=\"msg_centrato\">Non ci sono aule disponibili</p>";

    return $lista_aule;
}

function build_filtri_aule(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT genre FROM room ORDER BY genre ASC";
    $result_query = $db->query($query);

    $genres = [];

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            if (!in_array($row_query['genre'], $genres)) {
                $genres[] = $row_query['genre'];
            }
        }
    }

    $selected_genre = isset($_GET['genre']) ? $_GET['genre'] : '';

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $filtri_aule = "<form method='get' class='form_filtri' action='aule-studio-virtuali.php' name='filtri_aule'>
                        <div>
                            <label for='genre'>Genere: </label>
                            <select id='genre' name='genre'>
                                <option value=''>Tutti</option>";
    foreach ($genres as $genre) {
        $selected = $genre == $selected_genre ? "selected" : "";
        $filtri_aule .= "<option value='".$genre."' ".$selected.">".$genre."</option>";
    }

    $filtri_aule .= "</select>
                    </div>
                    <div>
                        <button class='interact' type='submit' aria-label='Bottone di ricerca per filtri'>Filtra</button>
                    </div>
                </form>";

    $filtri_aule .= "<form method='get' class='form_search' action='aule-studio-virtuali.php' name='search_aule'>
                        <div>
                            <label for='search'>Cerca: </label>
                            <input type='text' id='search' name='search' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                            <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                        </div>
                    </form>";

    return $filtri_aule;
}

function build_lista_aule_filter($categoria){
    $db = new Servizio;
    $db->apriconn();

    // Inizio la query di base
    $query = "SELECT * FROM room WHERE 1=1";

    // Aggiungo condizioni dinamiche in base ai filtri selezionati
    if (!empty($categoria)) {
        $query .= " AND LOWER(genre) = LOWER('$categoria')";
    }

    // Ordino i risultati
    $query .= " ORDER BY created_at DESC";

    $result_query = $db->query($query);

    $lista_aule = "";

    if($result_query->num_rows > 0){
        $lista_aule = "<div class=\"product-list\">";
        while($row_query = $result_query->fetch_assoc()){
            $lista_aule .= "<ul class=\"product-card\">
                                    <li class=\"product-info\">
                                        <h3>".$row_query['name']. "</h3>
                                        <p><b>Categoria: </b>".$row_query['genre']. "</p>
                                        <p><b>Creata il: </b>".$row_query['created_at']. "</p>
                                        <p><b>Creata da: </b>".$row_query['created_by']. "</p>
                                    </li>";
            if($_SESSION['Username'] == $row_query['created_by']){
                $lista_aule .= "<form method='post' action='aule-studio-virtuali.php' name='elimina_aula'>
                                    <div>
                                        <input type='hidden' name='id_aula' value='".$row_query['id']."' />
                                        <button class=\"deletebtn\" type='submit' name='submit_elimina_aula' aria-label='Elimina aula di \"".$row_query['name']."\"'>Elimina</button>
                                    </div>
                                </form>";
            }
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\"&room_name=urlencode(\"".toglispan($row_query['name'])."\")' name='vedi_aula'>
                                    <div>
                                        <input type='hidden' name='room_name' value='".urlencode(toglispan($row_query['name']))."' />
                                        <button class=\"loginbtn\" type='submit' name='room_code' value='".$row_query['id']."' aria-label='Entra in aula di \"".$row_query['name']."\"'>Entra in aula</button>
                                    </div>
                                </form>
                            </ul>";
        }
        $lista_aule .= "</div>";
    }else
        $lista_aule .= "<p class=\"msg_centrato\">Non ci sono aule disponibili per i filtri selezionati</p>";

    return $lista_aule;    
}

function build_lista_aule_search($search){
    $db = new Servizio;
    $db->apriconn();

    $search = strtolower($search);

    $query = "SELECT * FROM room WHERE LOWER(name) LIKE '%$search%' OR LOWER(genre) LIKE '%$search%' OR LOWER(created_by) LIKE '%$search%' ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $lista_aule = "";

    if($result_query->num_rows > 0){
        $lista_aule = "<div class=\"product-list\">";
        while($row_query = $result_query->fetch_assoc()){
            $lista_aule .= "<ul class=\"product-card\">
                                    <li class=\"product-info\">
                                        <h3>".$row_query['name']. "</h3>
                                        <p><b>Categoria: </b>".$row_query['genre']. "</p>
                                        <p><b>Creata il: </b>".$row_query['created_at']. "</p>
                                        <p><b>Creata da: </b>".$row_query['created_by']. "</p>
                                    </li>";
            if($_SESSION['Username'] == $row_query['created_by']){
                $lista_aule .= "<form method='post' action='aule-studio-virtuali.php' name='elimina_aula'>
                                    <div>
                                        <input type='hidden' name='id_aula' value='".$row_query['id']."' />
                                        <button class=\"deletebtn\" type='submit' name='submit_elimina_aula' aria-label='Elimina aula di \"".$row_query['name']."\"'>Elimina</button>
                                    </div>
                                </form>";
            }
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\"&room_name=urlencode(\"".toglispan($row_query['name'])."\")' name='vedi_aula'>
                                    <div>
                                        <input type='hidden' name='room_name' value='".urlencode(toglispan($row_query['name']))."' />
                                        <button class=\"loginbtn\" type='submit' name='room_code' value='".$row_query['id']."' aria-label='Entra in aula di \"".$row_query['name']."\"'>Entra in aula</button>
                                    </div>
                                </form>
                            </ul>";
        }
        $lista_aule .= "</div>";
    }else
        $lista_aule .= "<p class=\"msg_centrato\">Non ci sono aule disponibili per la ricerca effettuata</p>";

    return $lista_aule;
}

function contrassegnaParoleInglesi($testo) {
    // Sostituisce i tag [en] e [/en] con <span lang="en"> e </span>
    $testoConTag = preg_replace('/\[en\](.*?)\[\/en\]/', '<span lang="en">$1</span>', $testo);
    return $testoConTag;
}

function cambialospan($testo){
    // Sostituisce i tag <span lang="en"> e </span> con [en] e [/en]
    $testo = str_replace("<span lang=\"en\">", "[en]", $testo);
    $testo = str_replace("</span>", "[/en]", $testo);
    return $testo;
}

function toglispan($testo){
    // Sostituisce i tag <span lang="en"> e </span> con [en] e [/en]
    $testo = str_replace("<span lang=\"en\">", "", $testo);
    $testo = str_replace("</span>", "", $testo);
    return $testo;
}