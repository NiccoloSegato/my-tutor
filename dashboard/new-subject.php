<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to profile page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
    header("location: profile.php");
    exit;
}

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'fuoriklasse_new');

/* Attempt to connect to MySQL database */
$pdo = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($pdo === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Define variables and initialize with empty values
$name = $grade = "";
$errormsg = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["sub-name-field"]) && isset($_POST["sub-grade-field"])) {
        $name = htmlspecialchars($_POST["sub-name-field"]);
        $grade = htmlspecialchars($_POST["sub-grade-field"]);
        if(strlen($name) <= 0 || strlen($grade) <= 0){
            $errormsg = "Compila tutti i campi necessari";
        }
        else {
            $tutorid = $_SESSION["tutorid"];
            $query = $pdo->prepare("INSERT INTO subject (name, grade, tutor) VALUES (?, ?, ?)");
            $query->bind_param('ssi', $name, $grade, $tutorid);
            if($query->execute()) {
                // Subject correctly inserted
                header("location: index.php?sm=newsubject");
            }
            else {
                $errormsg = "Errore interno, riprova per favore";
            }
        }
    }
    else {
        // Not all post fields included
    }
}
?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.png" sizes="32x32">
        <title>Nuova materia - FuoriKLASSE</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/signup.css?v=2">
        <script src="scripts/signup.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="FuoriKLASSE">
        </header>
        <div id="globalcontainer">
            <h1>Aggiungi una tua materia</h1>
            <p>📚 Proprio come a scuola o all'università, le materie sono gli ambiti di studi dei quali vuoi dare ripetizioni. Ad esempio puoi creare la materia "Matematica" per dare ripetizioni di... Matematica! Scegli poi il <strong>grado</strong>, ad esempio "Scuole Medie" o "Università" e salva tutto per creare la tua prima lezione.</p>

            <form id="signup-form" method="POST">
                <?php if(strlen($errormsg) > 0) {
                    echo '<div id="loginerror"><p>' . $errormsg . '</p></div>';
                }
                ?>
                <label for="sub-name-field">Nome della materia</label>
                <p>Ad esempio "Geografia", oppure "Fisica 1"</p>
                <input type="text" name="sub-name-field" id="sub-name-field" placeholder="Inserisci il nome della materia...">

                <label for="sub-grade-field">Grado</label>
                <p>Ad esempio "Scuole Superiori - Biennio" oppure "Università"</p>
                <input type="text" name="sub-grade-field" id="sub-grade-field" placeholder="Inserisci il grado...">

                <button type="submit">Salva</button>
            </form>
        </div>
    </body>
</html>