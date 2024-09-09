function openBanDialog(username) {
    getElementByIdWithScreenCheck('banUsername').value = username;
    getElementByIdWithScreenCheck('banDialog').classList.remove('hideall');
}

function closeBanDialog() {
    getElementByIdWithScreenCheck('banDialog').classList.add('hideall');
}

// Chiudo la finestra di dialogo se l'utente clicca fuori dalla finestra
window.onclick = function(event) {
    var modal = getElementByIdWithScreenCheck('banDialog');
    if (event.target == modal) {
        modal.classList.add('hideall');
    }
}

function getElementByIdWithScreenCheck(baseId) {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var id = (isMobile || window.innerWidth<600) ? baseId + '_mobile' : baseId;
    return document.getElementById(id);
}