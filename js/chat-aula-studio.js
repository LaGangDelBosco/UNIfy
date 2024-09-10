document.addEventListener('DOMContentLoaded', function() {
    const roomCode = getQueryParameter('room_code'); // Supponendo che il codice della stanza venga passato come parametro nella query string
    getElementByIdWithScreenCheck('roomDisplayName').innerText = roomName.replace(/\+/g, ' '); // Sostituisce i caratteri "+" con spazi

    if (roomCode) {
        loadChatMessages(roomCode);
        setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso

        // Carica nuovi messaggi periodicamente
        setInterval(function() {
            loadChatMessages(roomCode);
        }, 3000); // Aggiorna ogni 3 secondi
    }

    getElementByIdWithScreenCheck('chatMessageRoom').addEventListener('keydown', function(event) {
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
            getElementByIdWithScreenCheck('chatMessagesRoom').innerHTML = xhr.responseText;
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
    var chatContainer = getElementByIdWithScreenCheck('chatMessagesRoom');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function sendMessage(roomCode) {
    console.log('Invio di un messaggio nella stanza:', roomCode);
    var message = getElementByIdWithScreenCheck('chatMessageRoom').value;
    if (message.trim() === '') return;

    var xhr = new XMLHttpRequest();
    console.log('Invio del messaggio:', message);
    xhr.open('POST', 'send-room-message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('Messaggio inviato con successo:', xhr.responseText);
            getElementByIdWithScreenCheck('chatMessageRoom').value = '';
            loadChatMessages(roomCode); // Ricarica i messaggi della chat
            setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso
        } else {
            console.error('Errore nell\'invio del messaggio:', xhr.status, xhr.statusText);
        }
        console.log('Risposta del server:', xhr.responseText);
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

function getElementByIdWithScreenCheck(baseId) {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var id = (isMobile || window.innerWidth<600) ? baseId + '_mobile' : baseId;
    return document.getElementById(id);
}
