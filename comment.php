<?php
// Includi il file di configurazione del database
require_once 'globale.php';

$db = new Servizio();
$db = $db->apriconn();

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera i dati inviati tramite POST
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $username = $_SESSION['Username']; // Assumendo che l'utente sia loggato e il nome utente sia memorizzato nella sessione

    // Verifica che i dati non siano vuoti
    if (!empty($post_id) && !empty($comment) && !empty($username)) {
        // Prepara la query SQL per inserire il commento nel database
        $query = "INSERT INTO comment (post_id, username, content, created_at) VALUES (?, ?, ?, NOW())";

        // Prepara la dichiarazione
        if ($stmt = $db->prepare($query)) {
            // Associa i parametri
            $stmt->bind_param('iss', $post_id, $username, $comment);

            // Esegui la query
            if ($stmt->execute()) {
                // Invia una risposta di successo
                $response = [
                    'success' => true,
                    'username' => $username,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else {
                // Invia una risposta di errore
                $response = [
                    'success' => false,
                    'message' => 'Errore durante l\'invio del commento'
                ];
            }

            // Chiudi la dichiarazione
            $stmt->close();
        } else {
            // Invia una risposta di errore
            $response = [
                'success' => false,
                'message' => 'Errore nella preparazione della query'
            ];
        }
    } else {
        // Invia una risposta di errore
        $response = [
            'success' => false,
            'message' => 'Dati mancanti'
        ];
    }

    // Invia la risposta come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    $db->close();
}
