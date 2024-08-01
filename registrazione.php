<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(isset($_POST['username']) && isset($_POST['nome']) && isset($_POST['cognome']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['conferma_password']) && isset($_POST['telefono']) && isset($_POST['data_nascita'])) {
    $username = $_POST['username'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $conferma_password = $_POST['conferma_password'];
    $telefono = $_POST['telefono'];
    $data_nascita = $_POST['data_nascita'];

    if($password != $conferma_password) {
        $error = "Le password non coincidono";
    } 
    else{
        if($db->registrazione($username, $nome, $cognome, $password, $email, $telefono, $data_nascita)) {
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
?>