// Funzione per controllare la larghezza dello schermo e mostrare/nascondere il contenuto
function toggleContent() {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var desktopContents = document.getElementsByClassName("desktop-content");
    var mobileContents = document.getElementsByClassName("mobile-content");
  

    for (var i = 0; i < desktopContents.length; i++) {
      if (isMobile || window.innerWidth<600) {
          desktopContents[i].classList.add("hideall");
      } else {
          desktopContents[i].classList.remove("hideall");
      }
    }

    for (var i = 0; i < mobileContents.length; i++) {
      if (isMobile || window.innerWidth<600) {
          mobileContents[i].classList.remove("hideall");
      } else {
          mobileContents[i].classList.add("hideall");
      }
    }
console.log("toggleContent è stato chiamato");
console.log("isMobile è", isMobile);
console.log("window.innerWidth è", window.innerWidth);
console.log("Ci sono", desktopContents.length, "elementi con la classe desktop-content");
console.log("Ci sono", mobileContents.length, "elementi con la classe mobile-content");
  }
  
  // Chiama la funzione al caricamento della pagina e ogni volta che la finestra viene ridimensionata
  //toggleContent();
  window.addEventListener("load", toggleContent);
  window.addEventListener("resize", toggleContent);
