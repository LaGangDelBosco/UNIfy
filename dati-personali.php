<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_modifica_dati_personali'])){
    $nome = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $location = $_POST['location'];
    $website = $_POST['website'];
    if($_FILES['profile_picture_path']['size'] > 0){
        $profile_picture_path = $_FILES['profile_picture_path']['tmp_name'];
        $profile_picture_name = $_FILES['profile_picture_path']['name'];
        $profile_picture_size = $_FILES['profile_picture_path']['size'];
        $profile_picture_type = $_FILES['profile_picture_path']['type'];
        $profile_picture_error = $_FILES['profile_picture_path']['error'];
        $profile_picture = file_get_contents($profile_picture_path);
        $profile_picture = "./media/profile-pictures/" . $username . "_" . time() . "_" . $profile_picture_name;
        if(!move_uploaded_file($profile_picture_path, $profile_picture))
            $profile_picture = null;
    }
    else
        $profile_picture = null;

    if($profile_picture != null) {
        if($db->modifica_dati_personali($username, $nome, $email, $bio, $gender, $birthdate, $location, $website, $profile_picture)) {
            header("Location: ./dati-personali.php?messaggio=Dati modificati con successo");
            exit();
        }
        else {
            header("Location: ./dati-personali.php?messaggio=Errore nella modifica dei dati 1");
            exit();
        }
    }
    if($db->modifica_dati_personali($username, $nome, $email, $bio, $gender, $birthdate, $location, $website)) {
        header("Location: ./dati-personali.php?messaggio=Dati modificati con successo");
        exit();
    }
    else{
        header("Location: ./dati-personali.php?messaggio=Errore nella modifica dei dati 2");
        exit();
    }
}


$datipersonali_template = $template_engine->load_template("dati-personali-template.html");

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];

#$index_template->insert("build_keywords", build_keywords());
$datipersonali_template->insert("menu", build_menu());

$datipersonali_template->insert("mioprofilo", build_mioprofilo($username));

$datipersonali_template->insert("header", build_header());
$datipersonali_template->insert("goback", build_goback());
$datipersonali_template->insert("footer", build_footer());


echo $datipersonali_template->build();
