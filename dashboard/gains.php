<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to profile page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$servername = "localhost";
$usernameD = "root";
$password = "";
$dbname = "fuoriklasse_new";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    header("location: error.php");
}
?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.png" sizes="32x32">
        <title>I tuoi guadagni - FuoriKLASSE</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/your-subjects.css">
        <script src="scripts/jquery.js"></script>
        <script src="scripts/your-subjects.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="FuoriKLASSE">
        </header>
        <div id="globalcontainer">
            <h1>I tuoi guadagni</h1>
            <p style="margin-bottom: 5px;">💰 Qui puoi visualizzare le transazioni che le persone hanno fatto verso di te, controllare i tuoi guadagni e richiedere il prelivo verso il tuo conto</p>
            
            <!-- TODO: complete this page after payment system implementation -->
        </div>
    </body>
</html>