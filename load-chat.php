<?php
require_once ("./globale.php");

$id_annuncio = $_GET['id_annuncio'];
$sender_username = $_SESSION['Username'];
$receiver_username = $_GET['receiver_username'];

if (empty($id_annuncio) || empty($sender_username) || empty($receiver_username)) {
    http_response_code(400);
    echo 'Dati mancanti';
    exit;
}

$messages = $db->get_chat_messages($id_annuncio, $sender_username, $receiver_username);

foreach ($messages as $message) {
    $class = $message['sender_username'] === $sender_username ? 'sent' : 'received';
    echo "<div class=$class><b>{$message['sender_username']}:</b> {$message['message']}</div>";
}
?>