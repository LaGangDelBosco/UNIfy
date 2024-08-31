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

    if(isset($_SESSION['Username']) && ($_SESSION['Username']=="admin")){
        $menu_items=array(
            "<span lang=\"en\">Home</span>" => "index.php",
            "Il mio profilo" => "mio-profilo.php",
            "Amici" => "amici.php",
            "Compro/Vendo Libri" => "compro-vendo-libri.php",
            "I miei dati personali" => "dati-personali.php",
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
                "Compro/Vendo Libri" => "compro-vendo-libri.php",
                "I miei dati personali" => "dati-personali.php",
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
            $like_count = $db->get_like_count($row_query['post_id']);
            $post_id = $row_query['post_id'];
            $lista_post .= "<ul class=\"singolo_post\">";


            $lista_post .=     "<li><a href=\"profilo.php?user=".$row_query['username']."\">".$row_query['username']."</a></li>";
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

function build_mioprofilo($username){ // TODO: cambiare nome alla funzione
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
                        <li><img class='profile-picture' src = ".$row_query['profile_picture_path']." alt=\"\"/></li>  
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
                                <li><img class='profile-picture'  src = ".$row_query_user['profile_picture_path']." alt=\"\"/></li>  
                                <li><b>Nome: </b>".$row_query_user['name']."</li>
                                <li><b>Username: </b>".$amico."</li>
                                <li><b>Biografia: </b>".$row_query_profile['bio']."</li>
                                <li>
                                    <fieldset>
                                        <legend>Rimuovi amicizia a ".$amico."</legend>
                                        <form method='post' action='amici.php' name='rimuovi_amicizia'>
                                            <div>
                                                <input type='hidden' name='amico' value='".$amico."' />
                                            </div>
                                            <button class=\"loginbtn\" type='submit' name='submit_rimuovi_amicizia'>Rimuovi Amicizia</button>
                                        </form>
                                    </fieldset>
                                </li>
                            </ul><hr/>"; //rimuovi_amicizia DA IMPLEMENTARE
    
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

        $modifica_dati_personali = "<form class='form_box' method='post' action='dati-personali.php' name='modifica_dati_personali' enctype='multipart/form-data'>
                                        <div>
                                            <label for='name'>Nome</label><br>
                                            <input type='text' id='name' name='name' value='".$row_query['name']."' required /><br/>
                                            <label for='email'>Email</label><br/>
                                            <input type='email' id='email' name='email' value='".$row_query['email']."' required /><br/>
                                            <label for='username'>Username</label><br/>
                                            <input type='text' id='username' name='username' value='".$row_query['username']."' readonly /><br/>
                                            <label for='bio'>Biografia</label><br/>
                                            <input type='text' id='bio' name='bio' value='".$row_query_profile['bio']."' required /><br/>
                                            <label for='gender'>Genere</label><br/>
                                            <select id='gender' name='gender' required>
                                                <option value='M'".($row_query['gender']=='M'?' selected':'').">M</option>
                                                <option value='F'".($row_query['gender']=='F'?' selected':'').">F</option>
                                                <option value='Non specificato'".($row_query['gender']=='Non specificato'?' selected':'').">Non specificato</option>
                                            </select><br/>
                                            <label for='birthdate'>Data di nascita</label><br/>
                                            <input type='date' id='birthdate' name='birthdate' value='".$row_query['birthdate']."' required /><br/>
                                            <label for='location'>Luogo</label><br>
                                            <input type='text' id='location' name='location' value='".$row_query_profile['location']."' required /><br/>
                                            <label for='website'>Sito Web</label><br>
                                            <input type='text' id='website' name='website' value='".$row_query_profile['website']."' required /><br/>
                                            <label for='profile_picture_path'>Cambia immagine profilo</label><br>
                                            <input type='file' id='profile_picture_path' name='profile_picture_path' accept='image/*'/><br/>
                                            <fieldset>
                                                <legend>Bottone Modifica Dati Personali</legend>
                                                <button class=\"loginbtn\" type='submit' name='submit_modifica_dati_personali'>Modifica</button>
                                            </fieldset>
                                        </div>
                                    </form>";
    }

    return $modifica_dati_personali;
}

function build_mypost($username){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM post WHERE username = '$username' ORDER BY created_at DESC";
    $result_query = $db->query($query);

    $mypost = "";

    if($result_query->num_rows > 0){
        while($row_query = $result_query->fetch_assoc()){
            $post_id = $row_query['post_id'];
            $like_count = $db->get_like_count($post_id);

            $mypost .= "<ul class=\"singolo_post\">
                            <li><a href=\"\">".$row_query['username']."</a></li>
                            <li>".$row_query['created_at']."</li>
                            <li>".$row_query['content']."</li>";

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

function build_lista_libri(){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT * FROM books ORDER BY created_at DESC";
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
                                        <p><b>Descrizione: </b>".$row_query['description']. "</p>
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
        $lista_libri .= "<p>Non ci sono libri in vendita</p>";

    return $lista_libri;
}


function build_annuncio($annuncio){
    $db = new Servizio;
    $db->apriconn();

    $annuncio = "<ul>
                    <li>
                        <img src=".$annuncio['cover_path']." alt=\"\"/>
                    </li>
                    <ul>
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
                    <button class=\"deletebtn\" type=\"submit\" name=\"submit_elimina_annuncio\">Elimina annuncio</button>
                </div>
            </form>";
    return $buttons;
}

function build_buttons_otherbook($id_annuncio, $username){
    $buttons = "<form id='contactForm_".$id_annuncio."' onsubmit='openChat(".$id_annuncio.", \"".$username."\"); return false;'>
                    <div>
                        <button class=\"loginbtn\" type=\"submit\">Contatta il venditore</button>
                    </div>
                </form>";
    return $buttons;
}

function build_tabella_interessati($id_annuncio){
    $db = new Servizio;
    $db->apriconn();

    $query = "SELECT DISTINCT sender_username FROM chat_messages WHERE id_annuncio = $id_annuncio AND sender_username != '".$_SESSION['Username']."'";
    $result_query = $db->query($query);

    $tabella_interessati = "<h3> Interessati </h3>";

    if($result_query->num_rows > 0){
        $tabella_interessati .= "<table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>";
        while ($contact = $result_query->fetch_assoc()) {
        $tabella_interessati .= "<tr>
                            <td>".$contact['sender_username']."</td>
                            <td>
                                <button class=\"loginbtn\" onclick='openChat(".$id_annuncio.", \"".$contact['sender_username']."\")'>Apri Chat</button>
                            </td>
                        </tr>";
        }
        $tabella_interessati .= "</tbody></table>";
    }
    else
        $tabella_interessati .= "<p>Non ci sono interessati</p>";

    return $tabella_interessati;
}