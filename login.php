<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if (isset($_POST['email']) && isset($_POST['password'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = $db->login($email, $password);
    if(!$result){
        echo $db->err_text;
        $db->err_text = "";
        header("Location: ./login.php?messaggio=Nome utente o password errata");
    }else{
        $_SESSION['Email'] = $email; // $_SESSION['Email'] sostituisce la variabile di sessione per l'utente
        if (isset($_SESSION['redirect_url'])){
            header("Location: " . $_SESSION['redirect_url']);
            unset($_SESSION['redirect_url']);
        }
        else
            header("Location: ./index.php");
    }
}

$login_template = $template_engine->load_template("login-template.html");

$login_template->insert("header", build_header());
$login_template->insert("goback", build_goback());
$login_template->insert("footer", build_footer());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    $login_template-> insert("messaggio", '<div id="messaggioerrore">' . $messaggio . '</div>');
}
else $login_template-> insert("messaggio", "");

echo $login_template->build();
?>