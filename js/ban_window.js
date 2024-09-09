function openBanDialog(username) {
    var banDialog = getElementByIdWithScreenCheck('banDialog');
    getElementByIdWithScreenCheck('banUsername').value = username;
    banDialog.classList.add('modal-view');
}

function closeBanDialog() {
    var banDialog = getElementByIdWithScreenCheck('banDialog');
    banDialog.classList.remove('modal-view');
}

// Chiudo la finestra di dialogo se l'utente clicca fuori dalla finestra
window.onclick = function(event) {
    var modal = getElementByIdWithScreenCheck('banDialog');
    if (event.target == modal) {
        banDialog.classList.remove('modal-view');
    }
}

function getElementByIdWithScreenCheck(baseId) {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var id = (isMobile || window.innerWidth<600) ? baseId + '_mobile' : baseId;
    return document.getElementById(id);
}