<?php
$root= ".";
require_once ("./globale.php");

if(isset($_SESSION['Username'])){
    unset($_SESSION['Username']);
    header("Location: ./index.php");
}else{
    header("Location: ./index.php");
}
