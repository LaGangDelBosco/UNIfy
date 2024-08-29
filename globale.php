<?php
const MB = 1048576;

session_start();

# Per visualizzare gli errori di PHP (su Docker non si vedevano)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log('Debug message');
// Fine istruzioni per visualizzare gli errori di PHP


date_default_timezone_set("Europe/Rome");
$root = '.';
require_once($root . "/lib/template_engine.php");
require_once($root . "/lib/builder.php");
require_once($root . "/lib/dbconfig.php");

// apertura connessione al database
$db = new Servizio();
$db->apriconn();
if ($db->err_code){echo("Si è verificato un errore:".$db->err_text);}

$template_engine = new TemplateEngine();
