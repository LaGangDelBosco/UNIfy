document.addEventListener('DOMContentLoaded', function() {
    const roomCode = getQueryParameter('room_code'); // Supponendo che il codice della stanza venga passato come parametro nella query string
    const roomName = getQueryParameter('room_name'); // Supponendo che anche il nome della stanza venga passato
    document.getElementById('roomDisplayName').innerText = roomName.replace(/\+/g, ' '); // Sostituisce i caratteri "+" con spazi

    if (roomCode) {
        loadChatMessages(roomCode);
        setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso

        // Carica nuovi messaggi periodicamente
        setInterval(function() {
            loadChatMessages(roomCode);
        }, 3000); // Aggiorna ogni 3 secondi
    }

    document.getElementById('chatMessageRoom').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage(roomCode);
        }
    });
});

function loadChatMessages(roomCode) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load-room-chat.php?room_code=' + roomCode, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('chatMessagesRoom').innerHTML = xhr.responseText;
        } else {
            console.error('Errore nel caricamento dei messaggi:', xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Errore di rete durante il caricamento dei messaggi.');
    };
    xhr.send();
}

function scrollToBottom() {
    var chatContainer = document.getElementById('chatMessagesRoom');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function sendMessage(roomCode) {
    var message = document.getElementById('chatMessageRoom').value;
    if (message.trim() === '') return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send-room-message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('chatMessageRoom').value = '';
            loadChatMessages(roomCode); // Ricarica i messaggi della chat
            setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso
        } else {
            console.error('Errore nell\'invio del messaggio:', xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Errore di rete durante l\'invio del messaggio.');
    };
    xhr.send('message=' + encodeURIComponent(message) + '&room_code=' + roomCode);
}

function getQueryParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}
