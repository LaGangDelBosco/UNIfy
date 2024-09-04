<?php
require_once ("./globale.php");

$message = $_POST['message'];
$id_annuncio = $_POST['id_annuncio'];
$sender_username = $_SESSION['Username'];
$receiver_username = $_POST['receiver_username'];

$db->send_chat_message($id_annuncio, $sender_username, $receiver_username, $message);
?>