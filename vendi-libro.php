<?php
$root= ".";
require_once ("./globale.php");

unset($_SESSION['redirect_url']);

if(!isset($_SESSION['Username'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit_vendi_libro'])){
    $venditore = $_SESSION['Username'];
    $titolo = $_POST['titolo'];
    $autore = $_POST['autore'];
    $categoria = $_POST['categoria'];
    $anno = $_POST['anno'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    if($_FILES['book_picture_path']['size'] > 0){
        $book_picture_path = $_FILES['book_picture_path']['tmp_name'];
        $book_picture_name = $_FILES['book_picture_path']['name'];
        $book_picture_size = $_FILES['book_picture_path']['size'];
        $book_picture_type = $_FILES['book_picture_path']['type'];
        $book_picture_error = $_FILES['book_picture_path']['error'];
        $book_picture = file_get_contents($book_picture_path);
        $book_picture = "./media/book-pictures/" . $username . "_" . time() . "_" . $book_picture_name;
        if(!move_uploaded_file($book_picture_path, $book_picture))
            $book_picture = null;
    }
    else
        $book_picture = null;

    if($book_picture != null) {
        if($db->vendi_libro($venditore, $titolo, $autore, $categoria, $anno, $descrizione, $prezzo, $book_picture)) {
            header("Location: compro-vendo-libri.php?messaggio=Libro aggiunto con successo");
            exit();
        }
        else {
            header("Location: vendi-libro.php?messaggio=Errore nella vendita del libro");
            exit();
        }
    }else{
    if($db->vendi_libro($venditore, $titolo, $autore, $categoria, $anno, $descrizione, $prezzo)){
        header("Location: compro-vendo-libri.php?messaggio=Libro aggiunto con successo");
        exit();
    }
    else{
        header("Location: vendi-libro.php?messaggio=Errore nella vendita del libro");
        exit();
    }
}
}


$vendilibro_template = $template_engine->load_template("vendi-libro-template.html");

$vendilibro_template->insert("menu", build_menu());

$vendilibro_template->insert("header", build_header());
$vendilibro_template->insert("goback", build_goback());
$vendilibro_template->insert("footer", build_footer());


echo $vendilibro_template->build();
