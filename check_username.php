<?php
require_once './globale.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $db = new Servizio();
    $exists = $db->check_username($username);
    echo json_encode(['exists' => $exists]);
}
