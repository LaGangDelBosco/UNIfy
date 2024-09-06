<?php
require_once "./globale.php";

if(isset($_SESSION['error_code'])) {
    unset($_SESSION['error_code']);
}

if(isset($_GET['forced'])) {
    $forced = $_GET['forced'];
    $_SESSION['forced'] = $forced;
} else {
    $_SESSION['forced'] = 0;
}

if(isset($_GET['error'])) {
    $id = $_GET['error'];
    if($_SESSION['forced'] == 1) {
        $_SESSION['error_code'] = $id;
    } else {
        $_SESSION['error_code'] = http_response_code();
    }
    unset($_SESSION['forced']);
}

$error_template = $template_engine->load_template("error-template.html");

$error_template->insert_multiple("code_error", $_SESSION['error_code']);
$error_template->insert("message_error", build_error_message($_SESSION['error_code']));
$error_template->insert("menu", build_menu());
$error_template->insert("header", build_header());
$error_template->insert("goback", build_goback());
$error_template->insert("footer", build_footer());

echo $error_template->build();
