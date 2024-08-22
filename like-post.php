<?php
include 'globale.php';

if (!isset($_SESSION['Username'])) {
    echo json_encode(['success' => false, 'message' => 'Devi essere loggato per poter mettere like ad un post']);
    exit();
}

$db = new Servizio();
$db = $db->apriconn();

$username = $_SESSION['Username'];
$post_id = $_POST['post_id'];

$query = "SELECT * FROM likes WHERE post_id = ? AND username = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('is', $post_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $query = "DELETE FROM likes WHERE post_id = ? AND username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('is', $post_id, $username);
    $stmt->execute();

    $query = "SELECT COUNT(*) as likes_number FROM likes WHERE post_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['success' => true, 'message' => 'Like rimosso con successo', 'likes_number' => $row['likes_number']]);
} else {
    $query = "INSERT INTO likes (post_id, username) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('is', $post_id, $username);
    $stmt->execute();

    $query = "SELECT COUNT(*) as likes_number FROM likes WHERE post_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['success' => true, 'message' => 'Like aggiunto con successo', 'likes_number' => $row['likes_number']]);

    $db->close();
}
