<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(isset($_SESSION['Username']))
    $username = $_SESSION['Username'];
else {
    header('Location: error.php?id=401&forced=true');
    exit;
}

if(isset($_POST['submit_modifica_password'])){
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password != $confirm_password){
        header("Location: ./modifica-password.php?messaggio=Le password non coincidono");
    }else{
        if($db->check_password($username, $old_password)){   //da definire
            if($db->update_password($username, $new_password))  //da definire
                header("Location: ./modifica-password.php?messaggio=Password modificata con successo");
            else
                header("Location: ./modifica-password.php?messaggio=Errore nella modifica della password");
        }else
            header("Location: ./modifica-password.php?messaggio=Password errata");
    }
}

$modificapassword_template = $template_engine->load_template("modifica-password-template.html");

$modificapassword_template->insert_multiple("menu", build_menu());

$modificapassword_template->insert("header", build_header());
$modificapassword_template->insert("goback", build_goback());
$modificapassword_template->insert("footer", build_footer());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Password modificata con successo")
        $modificapassword_template-> insert_multiple("messaggio", ('<div class="messaggio">' . $messaggio . '</div>'));
    else
        $modificapassword_template-> insert_multiple("messaggio", ('<div class="messaggioerrore">' . $messaggio . '</div>'));
}
else $modificapassword_template-> insert_multiple("messaggio", (""));

echo $modificapassword_template->build();
?>