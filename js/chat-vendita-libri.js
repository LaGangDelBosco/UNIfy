console.log('File chat-vendita-libri.js caricato');

document.addEventListener('DOMContentLoaded', function() {
    var chatMessageInput = document.getElementById('chatMessage');
    if (chatMessageInput) {
        chatMessageInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Previene l'inserimento di un a capo nell'input
                sendMessage(); // Chiama la funzione per inviare il messaggio
            }
        });
    } else {
        console.error('Elemento con ID "chatMessage" non trovato');
    }
});

function openChat(id_annuncio, receiver_username) {
    var chatContainer = document.getElementById('chatContainer');
    chatContainer.classList.add('visible');
    chatContainer.dataset.idAnnuncio = id_annuncio;
    chatContainer.dataset.receiverUsername = receiver_username;
    loadChatMessages(id_annuncio, receiver_username);
    setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso
}

// Aggiorna i messaggi della chat ogni 4 secondi
setInterval(function() {
    var chatContainer = document.getElementById('chatContainer');
    if (chatContainer.classList.contains('visible')) {
        var id_annuncio = chatContainer.dataset.idAnnuncio;
        var receiver_username = chatContainer.dataset.receiverUsername;
        loadChatMessages(id_annuncio, receiver_username);
    }
}, 4000);

function closeChat() {
    var chatContainer = document.getElementById('chatContainer');
    chatContainer.classList.remove('visible');
}

function loadChatMessages(id_annuncio, receiver_username) {
    // Carica i messaggi della chat tramite AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load-chat.php?id_annuncio=' + id_annuncio + '&receiver_username=' + receiver_username, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('chatMessages').innerHTML = xhr.responseText;
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
    var chatContainer = document.getElementById('chatMessages');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}


function sendMessage() {
    var message = document.getElementById('chatMessage').value;
    if (message.trim() === '') return;

    var id_annuncio = document.getElementById('chatContainer').dataset.idAnnuncio;
    var receiver_username = document.getElementById('chatContainer').dataset.receiverUsername;

    // Invia il messaggio tramite AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send-message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('chatMessage').value = '';
            loadChatMessages(id_annuncio, receiver_username); // Ricarica i messaggi della chat
            setTimeout(scrollToBottom, 100); // Scorrimento automatico verso il basso
        } else {
            console.error('Errore nell\'invio del messaggio:', xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Errore di rete durante l\'invio del messaggio.');
    };
    xhr.send('message=' + encodeURIComponent(message) + '&id_annuncio=' + id_annuncio + '&receiver_username=' + receiver_username);
}

