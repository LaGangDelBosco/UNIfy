<?php
require_once ("./globale.php");

$room_code = $_GET['room_code'];

$query = "SELECT * FROM room_message WHERE room_code = '$room_code' ORDER BY timestamp ASC";
$result = $db->query($query);

$messages = $db->get_room_chat_message($room_code);

foreach ($messages as $message) {
    $class = $message['username'] === $_SESSION['Username'] ? 'sent' : 'received';
    echo "<div class=$class><b>{$message['username']}:</b> {$message['message']}</div>";
}
