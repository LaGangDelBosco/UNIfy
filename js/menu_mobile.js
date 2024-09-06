$(document).ready(function() {
    $('.menu-toggle').on('click', function() {
        $('.menu').slideToggle('.menu-open');
    });
});

// document.addEventListener("DOMContentLoaded", function() {
//     var menuToggle = document.getElementById("menu-toggle");
//     var hamburger_icon = document.getElementsByClassName("hamburger-icon");
//     var currentPath = window.location.pathname;

//     console.log(currentPath);

//     // Nascondi l'elemento se l'URL contiene "login" o "registrazione"
//     if (currentPath == "/login.php" || currentPath == "/registrazione.php") {
//         menuToggle.style.display = "none";
//         hamburger_icon[0].style.display = "none";
//     }
// });