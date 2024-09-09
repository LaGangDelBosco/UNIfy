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
            "Notifiche ".$notification_amount => "notifiche.php",
            "Utenti banditi" => "utenti-banditi.php",
            "Post nascosti" => "post-nascosti.php",
            "Compro/Vendo Libri" => "compro-vendo-libri.php",
            "Aule Studio Virtuali" => "aule-studio-virtuali.php",
            "Post che mi piacciono" => "post-piacciono.php",
            "Post commentati" => "post-commentati.php",
            "Amici" => "amici.php",
            "Il mio profilo" => "mio-profilo.php",
            "I miei dati personali" => "dati-personali.php",
        );
    }else{
        if(isset($_SESSION['Username'])){
            $menu_items=array(
                "<span lang=\"en\">Home</span>" => "index.php",
                "Notifiche ".$notification_amount => "notifiche.php",
                "Aule Studio Virtuali" => "aule-studio-virtuali.php",
                "Compro/Vendo Libri" => "compro-vendo-libri.php",
                "Post che mi piacciono" => "post-piacciono.php",
                "Post commentati" => "post-commentati.php",
                "Amici" => "amici.php",
                "Il mio profilo" => "mio-profilo.php",
                "I miei dati personali" => "dati-personali.php",
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
                                            <button class=\"interact\" type='submit' name='submit_nascondi_post' aria-label='Nascondi il post di'". $row_query['username']."' creato il'".$row_query['created_at']."'>Nascondi</button>
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
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\" aria-label=\"Bottone mi piace per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span>$like_count</span>
                                </li>
                                <li>
                                    <label class=\"label_commento\" id=\"label_comment_$post_id\" for=\"comment_$post_id\">Scrivi un commento:</label>
                                    <textarea class=\"textarea_commento_index\" id='comment_$post_id' placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\" aria-label=\"Bottone commenta per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Commenta</button>
                                    <div class='error' id='comment_error_$post_id'></div>
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
                                            <button class=\"interact\" type='submit' name='submit_nascondi_post' aria-label=\"Bottone per nascondere il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Nascondi</button>
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
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\" aria-label=\"Bottone mi piace per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span>$like_count</span>
                                </li>
                                <li>
                                        <label class=\"label_commento\" id=\"label_comment_mobile_$post_id\" for=\"comment_mobile_$post_id\">Scrivi un commento:</label>
                                        <textarea class=\"textarea_commento_index\" id='comment_mobile_$post_id' placeholder=\"Commenta\"></textarea>
                                        <button id='comment_button_mobile_$post_id' class=\"comment-interact\" aria-label=\"Bottone commenta per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Commenta</button>
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
                                <button class='loginbtn' type='submit' name='submit_rimuovi_amicizia' aria-label='Rimuovi amicizia a '".$amico.">Rimuovi Amicizia</button>
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
                                <button class='loginbtn' type='submit' name='submit_rimuovi_amicizia' aria-label='Rimuovi amicizia a '".$amico.">Rimuovi Amicizia</button>
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

        $modifica_dati_personali = "<form class='form_box' id='modifica_dati_personali' method='post' action='dati-personali.php' name='modifica_dati_personali'>
                                        <div>
                                            <label for='name'>Nome</label><br>
                                            <input type='text' id='name' name='name' value='".$row_query['name']."' required /><br/>
                                            <div class='error' id='name_error'></div>
                                        </div>
                                        <div>
                                            <label for='email'>Email</label><br/>
                                            <input type='email' id='email' name='email' value='".$row_query['email']."' required /><br/>
                                            <div class='error' id='email_error'></div>
                                        </div>
                                        <div>
                                            <label for='username'>Username</label><br/>
                                            <input type='text' id='username' name='username' value='".$row_query['username']."' readonly /><br/>
                                        </div>
                                        <div>
                                            <label for='gender'>Genere</label><br/>
                                            <select id='gender' name='gender' required>
                                                <option value='M'".($row_query['gender']=='M'?' selected':'').">M</option>
                                                <option value='F'".($row_query['gender']=='F'?' selected':'').">F</option>
                                                <option value='Non specificato'".($row_query['gender']=='Non specificato'?' selected':'').">Non specificato</option>
                                            </select><br/>
                                            <div class='error' id='gender_error'></div>
                                        </div>
                                        <div>
                                            <label for='birthdate'>Data di nascita</label><br/>
                                            <input type='date' id='birthdate' name='birthdate' value='".$row_query['birthdate']."' required /><br/>
                                            <div class='error' id='birthdate_error'></div>
                                        </div>
                                        <div>
                                            <fieldset>
                                                <legend>Bottone Modifica Dati Personali</legend>
                                                <button class=\"loginbtn\" type='submit' name='submit_modifica_dati_personali' aria-label='Bottone per confermare la modifica dei dati personali'>Modifica</button>
                                            </fieldset>
                                        </div>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var form = document.forms['modifica_dati_personali'];
                                            form.addEventListener('submit', function(event) {
                                                if(!modificadatipersonali()){
                                                    event.preventDefault();
                                                }
                                            });
                                        });
                                    </script>";
    }

    return $modifica_dati_personali;
}

function build_modifica_dati_personali_mobile($username){
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

        $modifica_dati_personali = "<form class='form_box' id='modifica_dati_personali_mobile' method='post' action='dati-personali.php' name='modifica_dati_personali_mobile'>
                                        <div>
                                            <label for='name_mobile'>Nome</label><br>
                                            <input type='text' id='name_mobile' name='name' value='".$row_query['name']."' required /><br/>
                                            <div class='error' id='name_mobile_error'></div>
                                        </div>
                                        <div>
                                            <label for='email_mobile'>Email</label><br/>
                                            <input type='email' id='email_mobile' name='email' value='".$row_query['email']."' required /><br/>
                                            <div class='error' id='email_mobile_error'></div>
                                        </div>
                                        <div>
                                            <label for='username_mobile'>Username</label><br/>
                                            <input type='text' id='username_mobile' name='username' value='".$row_query['username']."' readonly /><br/>
                                        </div>
                                        <div>
                                            <label for='gender_mobile'>Genere</label><br/>
                                            <select id='gender_mobile' name='gender' required>
                                                <option value='M'".($row_query['gender']=='M'?' selected':'').">M</option>
                                                <option value='F'".($row_query['gender']=='F'?' selected':'').">F</option>
                                                <option value='Non specificato'".($row_query['gender']=='Non specificato'?' selected':'').">Non specificato</option>
                                            </select><br/>
                                            <div class='error' id='gener_mobile_error'></div>
                                        </div>
                                        <div>
                                            <label for='birthdate_mobile'>Data di nascita</label><br/>
                                            <input type='date' id='birthdate_mobile' name='birthdate' value='".$row_query['birthdate']."' required /><br/>
                                            <div class='error' id='birthdate_mobile_error'></div>
                                        </div>
                                        <div>
                                            <fieldset>
                                                <legend>Bottone Modifica Dati Personali</legend>
                                                <button class=\"loginbtn\" type='submit' name='submit_modifica_dati_personali' aria-label='Bottone per confermare la modifica dei dati personali>Modifica</button>
                                            </fieldset>
                                        </div>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var form = document.forms['modifica_dati_personali_mobile'];
                                            form.addEventListener('submit', function(event) {
                                                if(!modificadatipersonalimobile()){
                                                    event.preventDefault();
                                                }
                                            });
                                        });
                                    </script>";
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

        $modifica_profilo = "<form class='form_box' id='modifica_profilo' method='post' action='mio-profilo.php' name='modifica_profilo' enctype='multipart/form-data'>
                                <div>
                                    <label for='bio'>Biografia</label><br>
                                    <textarea id='bio' name='bio' required>".cambialospan($row_query['bio'])."</textarea><br/>
                                    <div class='error' id='bio_error'></div>
                                </div>
                                <div>
                                    <label for='location'>Luogo</label><br/>
                                    <input type='text' id='location' name='location' value='".$row_query['location']."' required /><br/>
                                    <div class='error' id='location_error'>
                                </div>
                                <div>
                                    <label for='website'>Sito Web</label><br/>
                                    <input type='text' id='website' name='website' value='".$row_query['website']."' required /><br/>
                                    <div class='error' id='website_error'></div>
                                </div>
                                <div>
                                    <label for='profile_picture_path'>Foto Profilo</label><br/>
                                    <input type='file' id='profile_picture_path' name='profile_picture_path' accept='image/*' /><br/>
                                    <div class='error' id='profile_picture_path_error'></div>
                                </div>
                                    <fieldset>
                                        <legend>Bottone Modifica Profilo</legend>
                                        <button class=\"loginbtn\" type='submit' name='submit_modifica_profilo' aria-label='Bottone per confermare la modifica dei dati del profilo'>Modifica</button>
                                    </fieldset>
                                </div>
                            </form>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var form = document.forms['modifica_profilo'];
                                    form.addEventListener('submit', function(event) {
                                        if(!modificaprofilo()){
                                            event.preventDefault();
                                        }
                                    });
                                });
                            </script>";
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

        $modifica_profilo = "<form class='form_box' id='modifica_profilo_mobile' method='post' action='mio-profilo.php' name='modifica_profilo_mobile' enctype='multipart/form-data'>
                                <div>
                                    <label for='bio_mobile'>Biografia</label><br>
                                    <textarea id='bio_mobile' name='bio' required>".cambialospan($row_query['bio'])."</textarea><br/>
                                    <div class='error' id='bio_mobile_error'></div>
                                </div>
                                <div>
                                    <label for='location_mobile'>Luogo</label><br/>
                                    <input type='text' id='location_mobile' name='location_mobile' value='".$row_query['location']."' required /><br/>
                                    <div class='error' id='location_mobile_error'>
                                </div>
                                <div>
                                    <label for='website_mobile'>Sito Web</label><br/>
                                    <input type='text' id='website_mobile' name='website_mobile' value='".$row_query['website']."' required /><br/>
                                    <div class='error' id='website_mobile_error'></div>
                                </div>
                                <div>
                                    <label for='profile_picture_path_mobile'>Foto Profilo</label><br/>
                                    <input type='file' id='profile_picture_path_mobile' name='profile_picture_path_mobile' accept='image/*' /><br/>
                                    <div class='error' id='profile_picture_path_mobile_error'></div>
                                </div>
                                <div>
                                    <fieldset>
                                        <legend>Bottone Modifica Profilo</legend>
                                        <button class=\"loginbtn\" type='submit' name='submit_modifica_profilo' aria-label='Bottone per confermare la modifica dei dati del profilo'>Modifica</button>
                                    </fieldset>
                                </div>
                            </form>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var form = document.forms['modifica_profilo_mobile'];
                                    form.addEventListener('submit', function(event) {
                                        if(!modificaprofilomobile()){
                                            event.preventDefault();
                                        }
                                    });
                                });
                            </script>";
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
                                        <button class=\"interact\" type='submit' name='submit_nascondi_post' aria-label='Bottone per nascondere il post di '".$username."' creato il '".$row_query['created_at'].">Nascondi</button>
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
                                    <button class=\"like-interact\" data-post-id=\"". $post_id ."\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>";


            if($_SESSION['Username'] == $username) {
                $mypost .= "            <form method='post' action='mio-profilo.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina</button>
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
                            <button class=\"interact\" type='submit' name='submit_elimina_tutte_notifiche' aria-label='Bottone di eliminazione di tutte le notifiche'>Elimina tutte le notifiche</button>
                        </div>
                    </form>";

        while($row_query = $result_query->fetch_assoc()){
            $notifiche .= "<ul class=\"singola_notifica\">
                            <hr/>
                            <li><a href=\"profilo.php?user=".$row_query['sender_username']."\">".$row_query['sender_username']."</a></li>
                            <li>".$row_query['created_at']."</li>";
            $notifiche .= "<li>".$row_query['content']."</li>
                            <li>
                                <form method='post' action='notifiche.php' name='elimina_notifica'>
                                    <div>
                                        <input type='hidden' name='notification_id' value='".$row_query['notification_id']."' />
                                        <button class=\"interact\" type='submit' name='submit_elimina_notifica' aria-label='Bottone di cancellazione della notifica del tipo '".$row_query['type']."' proveniente da '".$row_query['sender_username'].">Elimina</button>
                                    </div>
                                </form>
                            </li>
                        </ul>";
        }
    } else {
        $notifiche .= "<p>Non è presente alcuna notifica</p>";
    }

    return $notifiche;
}//placeholder per continuare ad aggiungere aria-label a bottoni

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
                                        <button class=\"interact\" type='submit' name='submit_nascondi_post' aria-label='Bottone per nascondere il post di '".$username."' del '".$row_query['created_at'].">Nascondi</button>
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
                                    <ul>
                                        <li><button class=\"like-interact\" data-post-id=\"". $post_id ."\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                            <span class=\"numero_like\">$like_count</span>
                                        </li>
                                        <li id=\"commento_fieldset\"><label class=\"label_commento\" for=\"comment_mobile_$post_id\"> - Scrivi un commento:</label>
                                            <textarea id='comment_mobile_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                            <button id='comment_button_mobile_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>
                                        </li>";


            if($_SESSION['Username'] == $username) {
                $mypost .= "            <li><form method='post' action='mio-profilo.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina post</button>
                                        </div>
                                    </form></li>";
            }

            $mypost .= "        </ul></fieldset>
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
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."' aria-label=\"Vedi il tuo annuncio del libro ".togliSpan($row_query['title'])." creato il ".$row_query['created_at']."\" >Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."' aria-label=\"Vedi annuncio del libro ".togliSpan($row_query['title'])." dell'utente ".$row_query['username']."\">Vedi annuncio</button>
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
                    <button id=\"annuncio_button\" class=\"deletebtn\" type=\"submit\" name=\"submit_elimina_annuncio\" aria-label=\"Bottone che permette di eliminare questo annuncio\">Elimina annuncio</button>
                </div>
                <hr/>
            </form>";
    return $buttons;
}

function build_buttons_otherbook($id_annuncio, $username){
    $buttons = "<form id='contactForm_".$id_annuncio."' onsubmit='openChat(".$id_annuncio.", \"".$username."\"); return false;'>
                    <div>
                        <button id=\"annuncio_button\" class=\"loginbtn\" type=\"submit\" aria-label=\"Bottone che permette di contattare il venditore\">Contatta il venditore</button>
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

                $filtri_libri .= "<form method='get' id='search_libri' class='form_search' action='compro-vendo-libri.php' name='search_libri'>
                                    <div>
                                        <label for='search'>Cerca: </label>
                                        <input type='text' id='search_libro' name='search_libro' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                                        <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                                        <div class='error' id='searchlibro_error'></div>
                                    </div> 
                                </form>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var form = document.forms['search_libri'];
                                        form.addEventListener('submit', function(event) {
                                            if(!searchlibri()){
                                                event.preventDefault();
                                            }
                                        });
                                    });
                                </script>";

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

                $filtri_libri .= "<form method='get' id='search_libri_mobile' class='form_search' action='compro-vendo-libri.php' name='search_libri_mobile'>
                                    <div>
                                        <label for='search_mobile'>Cerca: </label>
                                        <input type='text' id='search_libro_mobile' name='search_libro_mobile' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                                        <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                                        <div class='error' id='searchlibromobile_error'></div>
                                    </div> 
                                </form>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var form = document.forms['search_libri_mobile'];
                                        form.addEventListener('submit', function(event) {
                                            if(!searchlibrimobile()){
                                                event.preventDefault();
                                            }
                                        });
                                    });
                                </script>";

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
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."' aria-label=\"Vedi il tuo annuncio del libro ".togliSpan($row_query['title'])." creato il ".$row_query['created_at']."\">Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."' aria-label=\"Vedi annuncio del libro ".togliSpan($row_query['title'])." dell'utente ".$row_query['username']."\">Vedi annuncio</button>
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
                                        <button class=\"loginbtn\" type='submit' name='myid' value='".$row_query['book_id']."' aria-label=\"Vedi il tuo annuncio del libro ".togliSpan($row_query['title'])." creato il ".$row_query['created_at']."\">Vedi il tuo annuncio</button>
                                    </div>
                                </form>";
            }else{
                $lista_libri .= "<form method='post' action='annuncio.php?id=".$row_query['book_id']."' name='contatta_venditore'>
                                    <div>
                                        <button class=\"loginbtn\" type='submit' name='id' value='".$row_query['book_id']."' aria-label=\"Vedi annuncio del libro ".togliSpan($row_query['title'])." dell'utente ".$row_query['username']."\">Vedi annuncio</button>
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
}

function build_liked_posts($username)
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
                                    <button class=\"like-interact\" data-post-id=\"$post_id\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>";

            if($_SESSION['Username'] == $post_username) {
                $liked_posts .= "            <form method='post' action='post-piacciono.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina</button>
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

function build_liked_posts_mobile($username)
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
                                    <ul>
                                        <li><button class=\"like-interact\" data-post-id=\"$post_id\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                            <span class=\"numero_like\">$like_count</span>
                                        </li>
                                        <li id=\"commento_fieldset\"><label class=\"label_commento\" for=\"comment_mobile_$post_id\"> - Scrivi un commento:</label>
                                            <textarea id='comment_mobile_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                            <button id='comment_button_mobile_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>
                                        </li>";
                                    
            if($_SESSION['Username'] == $post_username) {
                $liked_posts .= "            <li><form method='post' action='post-piacciono.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post_mobile\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina</button>
                                        </div>
                                    </form></li></ul>
                                    </fieldset>
                                    </li>";
            }else{
                $liked_posts .= "</fieldset></li>";
            }

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $liked_posts .= "<li id='comment_list_mobile_$post_id'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $liked_posts .= "<li><a href=\"profilo.php?user=$row_query_comment[username]\">$row_query_comment[username]</a></li>
                                    <li>$row_query_comment[created_at]</li>
                                    <li class=\"content_comm\">$row_query_comment[content]</li>";
                }
                $liked_posts .= "</ul></li>";
            } else {
                $liked_posts .= "<li id='comment_list_mobile_$post_id'></li>";
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
                                    <button class=\"like-interact\" data-post-id=\"$post_id\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>";

            if ($_SESSION['Username'] == $post_username) {
                $commented_posts .= "            <form method='post' action='post-commentati.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina</button>
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
}

function build_commented_posts_mobile($username){
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
                                    <ul>
                                        <li><button class=\"like-interact\" data-post-id=\"$post_id\" aria-label=\"Bottone di mi piace per il post di ".$username." creato il ".$row_query['created_at']."\">Mi piace</button>
                                            <span class=\"numero_like\">$like_count</span>
                                        </li>
                                        <li id=\"commento_fieldset\"><label class=\"label_commento\" for=\"comment_mobile_$post_id\"> - Scrivi un commento:</label>
                                            <textarea id='comment_mobile_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                            <button id='comment_button_mobile_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$username." creato il ".$row_query['created_at']."\">Commenta</button>
                                        </li>";
                                    
            if ($_SESSION['Username'] == $post_username) {
                $commented_posts .= "   <li><form method='post' action='post-commentati.php' name='elimina_post'>
                                        <div class = \"elimina_inline\"> 
                                            <input type='hidden' name='post_id' value='" . $row_query['post_id'] . "' />
                                            <button id=\"del_post_mobile\" class=\"interact\" type='submit' name='submit_elimina_post' aria-label=\"Bottone di cancellazione del tuo post creato il ".$row_query['created_at']."\">Elimina</button>
                                        </div>
                                    </form></li></ul>
                                    </fieldset>
                                    </li>";
            }else{
                $commented_posts .= "</fieldset></li>";
            }

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $commented_posts .= "<li id='comment_list_mobile_$post_id'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $commented_posts .= "<li><a href=\"profilo.php?user=$row_query_comment[username]\">$row_query_comment[username]</a></li>
                                    <li>$row_query_comment[created_at]</li>
                                    <li class=\"content_comm\">$row_query_comment[content]</li>";
                }
                $commented_posts .= "</ul></li>";
            } else {
                $commented_posts .= "<li id='comment_list_mobile_$post_id'></li>";
            }

            $commented_posts .= "</ul>";
        }
    }

    return $commented_posts;
}

function build_lista_utenti_banditi(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM user WHERE banned = 1";
    $result_query = $db->query($query);    

    $lista_utenti_banditi = "";

    if($result_query->num_rows > 0) {
        while ($row_query = $result_query->fetch_assoc()) {
            $query_img = "SELECT profile_picture_path FROM profile WHERE username = '" . $row_query['username'] . "'";
            $result_img = $db->query($query_img);
            $row_img = $result_img->fetch_assoc();
            $lista_utenti_banditi .= "<ul class=\"profilo\">
                                        <li><img class='profile-picture' src = " . $row_img['profile_picture_path']. " alt=\"\"/></li>  
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
                                                    <button class=\"loginbtn\" type='submit' name='submit_rimuovi_ban' aria-label='Bottone per rimuovere il ban all'utente '".$row_query['username'].">Rimuovi Ban</button>
                                                </form>
                                            </fieldset>
                                        </li>
                                    </ul><hr/>";
        }
    }else
        $lista_utenti_banditi .= "<p class=\"msg_centrato\">Non ci sono utenti banditi</p>";

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
                                            <button class=\"interact\" type='submit' name='submit_mostra_post' aria-label='Bottone per mostrare il post nascosto di '".$row_query['username']."' creato il '".$row_query['created_at'].">Mostra</button>
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
                                    <button class=\"like-interact\" data-post-id=\"" . $post_id . "\" aria-label=\"Bottone di mi piace per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Mi piace</button>
                                    <span class=\"numero_like\">$like_count</span>
                                    <label class=\"label_commento\" for=\"comment_$post_id\"> - Scrivi un commento:</label>
                                    <textarea id='comment_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                    <button id='comment_button_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Commenta</button>
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
    }else
        $post_nascosti .= "<p class=\"msg_centrato\">Non ci sono post nascosti</p>";

    return $post_nascosti;
}

function build_post_nascosti_mobile(){
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
                                            <button class=\"interact\" type='submit' name='submit_mostra_post' aria-label='Bottone per mostrare il post nascosto di '".$row_query['username']."' creato il '".$row_query['created_at'].">Mostra</button>
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
                                    <ul>
                                        <li>
                                            <button class=\"like-interact\" data-post-id=\"" . $post_id . "\" aria-label=\"Bottone di mi piace per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Mi piace</button>
                                            <span class=\"numero_like\">$like_count</span>
                                        </li>
                                        <li id=\"commento_fieldset\">
                                            <label class=\"label_commento\" for=\"comment_mobile_$post_id\"> Scrivi un commento:</label><br/>
                                            <textarea id='comment_mobile_$post_id' class=\"textarea_commento\" placeholder=\"Commenta\"></textarea>
                                             <button id='comment_button_mobile_$post_id' class=\"comment-interact\" aria-label=\"Bottone di commenta per il post di ".$row_query['username']." creato il ".$row_query['created_at']."\">Commenta</button>
                                        </li>
                                    </ul>
                                </fieldset>
                            </li>";

            $query = "SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY created_at DESC";
            $result_query_comment = $db->query($query);

            if ($result_query_comment->num_rows > 0) {
                $post_nascosti .= "<li id='comment_list_mobile_" . $post_id . "'><ul>";
                while ($row_query_comment = $result_query_comment->fetch_assoc()) {
                    $post_nascosti .= "<li><a href=\"profilo.php?user=" . $row_query_comment['username'] . "\">" . $row_query_comment['username'] . "</a></li>
                                    <li>" . $row_query_comment['created_at'] . "</li>
                                    <li class=\"content_comm\">" . $row_query_comment['content'] . "</li>";
                }
                $post_nascosti .= "</ul></li>";
            } else {
                $post_nascosti .= "<li id='comment_list_mobile_" . $post_id . "'></li>";
            }
            $post_nascosti .= "</ul>";
        }
    }else
        $post_nascosti .= "<p class=\"msg_centrato\">Non ci sono post nascosti</p>";

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
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\")' name='vedi_aula'>
                                    <div>
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

    $filtri_aule .= "<form method='get' id='search_aule' class='form_search' action='aule-studio-virtuali.php' name='search_aule'>
                        <div>
                            <label for='search'>Cerca: </label>
                            <input type='text' id='search_aula' name='search_aula' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                            <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                            <div class='error' id='searchaula_error'></div> 
                        </div>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var form = document.forms['search_aule'];
                            form.addEventListener('submit', function(event) {
                                if(!searchaule()){
                                    event.preventDefault();
                                }
                            });
                        });
                    </script>";

    return $filtri_aule;
}

function build_filtri_aule_mobile(){
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
                            <label for='genre_mobile'>Genere: </label>
                            <select id='genre_mobile' name='genre'>
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

    $filtri_aule .= "<form method='get' id='search_aule_mobile' class='form_search' action='aule-studio-virtuali.php' name='search_aule_mobile'>
                        <div>
                            <label for='search_mobile'>Cerca: </label>
                            <input type='text' id='search_aula_mobile' name='search' placeholder='Cerca...' value='".htmlspecialchars($search)."'>
                            <button class='interact' type='submit' aria-label='Bottone di ricerca per input'>Cerca</button>
                            <div class='error' id='searchaulamobile_error'></div>
                        </div>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var form = document.forms['search_aule_mobile'];
                            form.addEventListener('submit', function(event) {
                                if(!searchaulemobile()){
                                    event.preventDefault();
                                }
                            });
                        });
                    </script>";

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
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\")' name='vedi_aula'>
                                    <div>
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
            $lista_aule .= "<form method='get' action='aula.php?room_code=\"".$row_query['id']."\")' name='vedi_aula'>
                                    <div>
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

function build_error_message($error_code){
    switch ($error_code){
        case '400': // Bad request
            $error = "<p>Siamo spiacenti, ma la richiesta non può essere elaborata.</p>
                            <p>Questo potrebbe essere dovuto a un errore nell'indirizzo web che hai inserito o a un problema con la tua connessione internet.</p>
                                <p>Per favore, controlla l'indirizzo web e riprova.</p>
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
        case '401': // Unauthorized
            $error = "<p>Siamo spiacenti, ma non hai i permessi necessari per visualizzare questa pagina.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <p>Se sei un utente registrato, ti preghiamo di effettuare il <a href=\"login.php\">login</a>,</p>
                            <p>oppure, se non sei ancora registrato, ti invitiamo a <a href=\"registrazione.php\">registrarti</a>.</p>
                            <br>
                            <p>Altrimenti,</p>";
            break;
        case '403': // Forbidden
            $error = "<p>Siamo spiacenti, ma non hai i permessi necessari per visualizzare questa pagina.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <p>Se sei un amministratore, ti preghiamo di effettuare il <a href=\"login.php\">login</a>.</p>
                            <br>
                            <p>Altrimenti,</p>";
            break;
        case '404': // Not Found
            $error = "<p>Siamo spiacenti, ma la pagina che stai cercando non è stata trovata.
                            Questo potrebbe essere dovuto a un errore nell'indirizzo web che hai inserito o a un problema con la tua connessione internet. 
                                Per favore, controlla l'indirizzo web e riprova.</p>
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
        case '418': // I'm a teapot (easter egg)
            $error = "<p>Siamo spiacenti, ma il server non è in grado di preparare il caffè con una teiera.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
        case '500': // Internal Server Error
            $error = "<p>Siamo spiacenti, ma si è verificato un errore interno al server.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
        case '503': // Service Unavailable
            $error = "<p>Siamo spiacenti, ma il servizio non è al momento disponibile.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
        default:
            $error = "<p>Siamo spiacenti, ma si è verificato un errore sconosciuto.
                            <p>Se il problema persiste, ti preghiamo di contattare il nostro <span lang=\"en\">team</span> di supporto.</p>
                            <br>";
            break;
    }
    return $error;
}

function build_search($query){
    $db = new Servizio;
    $db->apriconn();

    $query_user = "SELECT * FROM user WHERE username LIKE '%$query%' OR name LIKE '%$query%'";
    $query_post = "SELECT * FROM post WHERE content LIKE '%$query%' OR username LIKE '%$query%'";

    $result_query_user = $db->query($query_user);
    $result_query_post = $db->query($query_post);

    $search_results = "";

    if($result_query_user->num_rows == 0 && $result_query_post->num_rows == 0){
        $search_results = "<div class=\"messaggio\">Nessun risultato trovato</div>";
    }

    if($result_query_user->num_rows > 0){
        $search_results .= "<h3>Risultati utenti</h3>";
        while($row_query_user = $result_query_user->fetch_assoc()){
            $search_results .= "<ul class=\"search-result\">
                                    <li><a href=\"profilo.php?user=".$row_query_user['username']."\">".$row_query_user['username']."</a></li>
                                    <li>".$row_query_user['name']."</li>
                                </ul>";
        }
    }

    if($result_query_post->num_rows > 0){
        $search_results .= "<h3>Risultati post</h3>";
        while($row_query_post = $result_query_post->fetch_assoc()){
            $search_results .= "<ul class=\"search-result\">
                                    <li><a href=\"profilo.php?user=".$row_query_post['username']."\">".$row_query_post['username']."</a></li>
                                    <li>".$row_query_post['created_at']."</li>
                                    <li>".$row_query_post['content']."</li>
                                </ul>";
        }
    }

    return $search_results;
}

function build_lista_suggeriti(){
    if (isset($_SESSION['Username'])){
        $username = $_SESSION['Username'];
    } else {
        return "";
    }

    $db = new Servizio;
    $db->apriconn();

    $result_friends = $db->get_amici($username);

    $friends = [];
    while ($row = $result_friends->fetch_assoc()) {
        if ($row['username_1'] != $username) {
            $friends[] = $row['username_1'];
        }
        if ($row['username_2'] != $username) {
            $friends[] = $row['username_2'];
        }
    }

    $mutual_friends = [];
    foreach ($friends as $friend) {
        $result_mutual = $db->get_amici_in_comune($friend);

        while ($row = $result_mutual->fetch_assoc()) {
            $mutual_friend = ($row['username_1'] == $friend) ? $row['username_2'] : $row['username_1'];
            if ($mutual_friend != $username && !in_array($mutual_friend, $friends)) {
                if (!isset($mutual_friends[$mutual_friend])) {
                    $mutual_friends[$mutual_friend] = 0;
                }
                $mutual_friends[$mutual_friend]++;
            }
        }
    }

    arsort($mutual_friends);

    $mutual_friends = array_slice($mutual_friends, 0, 5, true);
    
    $suggested_friends = "<h3>Potresti Conoscere</h3><ul>";
    foreach ($mutual_friends as $mutual_friend => $count) {
        if ($count > 1) {
            $suggested_friends .= "<li><a href=\"profilo.php?user=".$mutual_friend."\">".$mutual_friend."</a> - ".$count." amici in comune</li>";
        } else {
            $suggested_friends .= "<li><a href=\"profilo.php?user=".$mutual_friend."\">".$mutual_friend."</a> - ".$count." amico in comune</li>";
        }
    }
    $suggested_friends .= "</ul>";

    return $suggested_friends;
}