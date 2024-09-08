<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['Username'];

if(isset($_POST['submit_rimuovi_amicizia'])){
    $amico = $_POST['amico'];
    if($db->rimuovi_amicizia($username, $amico))
        header("Location: ./amici.php?messaggio=Amicizia rimossa con successo");
    else
        header("Location: ./amici.php?messaggio=Errore nella rimozione dell'amicizia");

}

$amici_template = $template_engine->load_template("amici-template.html");

$amici_template->insert_multiple("menu", build_menu());

if(isset($_GET['messaggio'])){
    $messaggio = htmlspecialchars($_GET['messaggio']);
    if($messaggio == "Errore nella rimozione dell'amicizia")
        $amici_template->insert_multiple("messaggio", "<div class='messaggioerrore'>" . $messaggio . "</div>");
    else
        $amici_template->insert_multiple("messaggio", "<div class='messaggio'>" . $messaggio . "</div>");
}else
    $amici_template->insert_multiple("messaggio", "");

$amici_template->insert_multiple("lista_amici", build_lista_amici($username));
$amici_template->insert_multiple("lista_amici_mobile", build_lista_amici_mobile($username));

$amici_template->insert("header", build_header());
$amici_template->insert("goback", build_goback());
$amici_template->insert("footer", build_footer());


echo $amici_template->build();