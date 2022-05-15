<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to profile page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

define('DB_SERVER', '89.46.111.38');
define('DB_USERNAME', 'Sql1068665');
define('DB_PASSWORD', '3863t3v631');
define('DB_NAME', 'Sql1068665_3');

/* Attempt to connect to MySQL database */
$pdo = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($pdo === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Define variables and initialize with empty values
$username = $password = "3863t3v631";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["signup-name-field"]) && isset($_POST["signup-surname-field"]) && isset($_POST["signup-city-field"]) && isset($_POST["signup-birthday-field"]) && isset($_POST["signup-email-field"]) && isset($_POST["signup-psw-field"])) {
        $email = htmlspecialchars($_POST["signup-email-field"]);
        $password = hash('sha256', htmlspecialchars($_POST["signup-psw-field"]));
        $name = htmlspecialchars($_POST["signup-name-field"]);
        $surname = htmlspecialchars($_POST["signup-surname-field"]);
        $city = htmlspecialchars($_POST["signup-city-field"]);
        $birthday = htmlspecialchars($_POST["signup-birthday-field"]);

        $query = $pdo->prepare("SELECT id FROM tutor WHERE email = ?");
        $query->bind_param('s', $email);
        if($query->execute()) {
            $result = $query->get_result();
            // Check if username exists, if yes then verify password
            if($result->num_rows == 1){
                // Already existing user
            }
            else {
                // Inserting user into database
                $queryrec = $pdo->prepare("INSERT INTO tutor (name, surname, city, birthday, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $queryrec->bind_param('ssssss', $name, $surname, $city, $birthday, $email, $password);
                if($queryrec->execute()) {
                    $userid = $queryrec->insert_id;
                    session_start();
                            
                    // Store data in session variables
                    $_SESSION["tutorid"] = $userid;
                    $_SESSION["loggedin"] = true;

                    header("location: index.php");
                }
            }
        }
    }
    else {
        // Not all post fields included
    }
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.png" sizes="32x32">
        <title>Registrati - FuoriKLASSE</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/signup.css?v=2">
        <script src="scripts/signup.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="FuoriKLASSE">
        </header>
        <div id="globalcontainer">
            <h1>Registrati su FuoriKLASSE</h1>
            <p>Inizia a dare ripetizioni e scopri come è semplice usare FuoriKLASSE per crescere 📚</p>

            <form id="signup-form" method="POST">
                <label for="signup-name-field">Nome</label>
                <input type="text" name="signup-name-field" id="signup-name-field" placeholder="Inserisci il tuo nome...">

                <label for="signup-surname-field">Cognome</label>
                <input type="text" name="signup-surname-field" id="signup-surname-field" placeholder="Inserisci il tuo cognome...">

                <label for="signup-city-field">Città</label>
                <input type="text" name="signup-city-field" id="signup-city-field" placeholder="Inserisci la città dove abiti...">

                <label for="signup-birthday-field">Data di nascita</label>
                <input type="date" name="signup-birthday-field" id="signup-birthday-field">

                <div class="form-divider"></div>

                <label for="signup-email-field">Email</label>
                <input type="email" name="signup-email-field" id="signup-email-field" placeholder="Inserisci la tua email...">

                <label for="signup-psw-field">Password</label>
                <input type="password" name="signup-psw-field" id="signup-psw-field" placeholder="Inserisci una password...">

                <input type="checkbox" id="signup-check-field" name="signup-check-field">
                <label for="signup-check-field" style="font-weight: 400;"> Ho letto e accettato i Termini e Condizioni e la Privacy Policy</label><br>

                <button type="button" onclick="checkFields()">Registrati</button>
            </form>
        </div>
    </body>
</html>