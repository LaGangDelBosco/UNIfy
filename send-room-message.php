<?php
require_once ("./globale.php");

$room_code = $_POST['room_code'];
$message = $_POST['message'];
$username = $_SESSION['Username'];

$query = "INSERT INTO room_message (room_code, username, message, timestamp) VALUES ('$room_code', '$username', '$message', NOW())";
$db->query($query);