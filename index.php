<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$index_template = $template_engine->load_template("index-template.html");

#$index_template->insert("build_keywords", build_keywords());
$index_template->insert("menu", build_menu());

function convert_youtube_links_to_iframe($text) {
    $pattern = '/(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i';
    $replacement = '<iframe width="560" height="315" src="https://www.youtube.com/embed/$4" frameborder="0" allowfullscreen></iframe>';
    return preg_replace($pattern, $replacement, $text);
}

if(isset($_POST['submit-public-post'])){
    $text = $_POST['text'];
    $media = $_FILES['media'];

    $text = convert_youtube_links_to_iframe($text);

    if($media['error'] == 0 && $db->check_media($media['type'], $media['size']) == "successo"){
        // filtra il nome del file
        $media['name'] = $db->filter_filename($media['name']);
        //$media['tmp_name'] = $db->filter_filename($media['tmp_name']);
        $media_path = "./media/user-media/" . $media['name'];

        // se $media_path supera i 100 caratteri ritorna errore
        if(strlen($media_path) > 100){
            header("Location: index.php?error=media_path_too_long");
        }

        move_uploaded_file($media['tmp_name'], $media_path);
        $db->inserisci_post($text, $_SESSION['Username'], $media_path);
    } else {
        $db->inserisci_post($text, $_SESSION['Username']);
    }
    $error = $media['error'];
    header("Location: index.php?error=$error");
}

if(isset($_POST['submit_nascondi_post'])){
    $id_post = $_POST['id_post'];
    $current_page = $_POST['current_page'];
    $db->nascondi_post($id_post);
    header("Location: $current_page");
    exit();
}

$index_template->insert("lista_post", build_lista_post());

$index_template->insert("header", build_header());
$index_template->insert("goback", build_goback());
$index_template->insert("footer", build_footer());


echo $index_template->build();
