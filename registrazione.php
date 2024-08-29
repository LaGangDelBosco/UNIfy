<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(isset($_POST['username']) && isset($_POST['nome_cognome']) && isset($_POST['email']) && isset($_POST['data_nascita']) && isset($_POST['gender']) && isset($_POST['password']) && isset($_POST['conferma_password'])) {
    $username = $_POST['username'];
    $nome_cognome = $_POST['nome_cognome'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $conferma_password = $_POST['conferma_password'];
    $data_nascita = $_POST['data_nascita'];

    if($password != $conferma_password) {
        $error = "Le password non coincidono";
    } 
    else{
        if($db->registrazione($username, $nome_cognome, $email, $password, $data_nascita, $gender)) {
            $error = "Registrazione avvenuta con successo";
            header("Location: index.php?messaggio=Registrazione avvenuta con successo! Effettua il login per accedere al tuo account.");
        } 
        else {
            $error = "Errore durante la registrazione";
            header("Location: registrazione.php?messaggio=Errore durante la registrazione");
        }
    }

}


$registrazione_template = $template_engine->load_template("registrazione-template.html");


$registrazione_template->insert("header", build_header());
$registrazione_template->insert("goback", build_goback());
$registrazione_template->insert("footer", build_footer());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Nome utente o password errata"){
        $registrazione_template-> insert("messaggio", ('<div id="messaggioerrore">' . $messaggio . '</div>'));
    }
    else
        $registrazione_template-> insert("messaggio", ('<div id="messaggio">' . $messaggio . '</div>'));
}
else $registrazione_template-> insert("messaggio", (""));

echo $registrazione_template->build();
