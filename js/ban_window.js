function openBanDialog(username) {
    getElementByIdWithScreenCheck('banUsername').value = username;
    getElementByIdWithScreenCheck('banDialog').style.display = 'block';
}

function closeBanDialog() {
    getElementByIdWithScreenCheck('banDialog').style.display = 'none';
}

// Chiudo la finestra di dialogo se l'utente clicca fuori dalla finestra
window.onclick = function(event) {
    var modal = getElementByIdWithScreenCheck('banDialog');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function getElementByIdWithScreenCheck(baseId) {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var id = (isMobile || window.innerWidth<600) ? baseId + '_mobile' : baseId;
    return document.getElementById(id);
}