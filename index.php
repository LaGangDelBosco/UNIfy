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

if(isset($_POST['submit-public-post'])){
    $text = $_POST['text'];
    $media = $_FILES['media'];

    if($media['error'] == 0 && $db->check_media($media['type'], $media['size']) == "successo"){
        $media_path = "./media/user-media/" . $media['name'];
        move_uploaded_file($media['tmp_name'], $media_path);
        $db->inserisci_post($text, $_SESSION['Username'], $media_path);
    } else {
        $db->inserisci_post($text, $_SESSION['Username']);
    }
    $error = $media['error'];
    header("Location: index.php?error=$error");

//    $post = $_POST['text'];
//    $db->inserisci_post($post, $_SESSION['Username']);
//    header("Location: index.php");
}

$index_template->insert("lista_post", build_lista_post());

$index_template->insert("header", build_header());
$index_template->insert("goback", build_goback());
$index_template->insert("footer", build_footer());


echo $index_template->build();
