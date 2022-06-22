<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to profile page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: profile.php");
    exit;
}

define('DB_SERVER', '89.46.111.249');
define('DB_USERNAME', 'Sql1644591');
define('DB_PASSWORD', 'TaPM8fXBfnAsWBA!!');
define('DB_NAME', 'Sql1644591_1');

/* Attempt to connect to MySQL database */
$pdo = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($pdo === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$loginerror = "";
// Define variables and initialize with empty values
$username = $password = "TaPM8fXBfnAsWBA!!";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["signup-email-field"]) && isset($_POST["signup-psw-field"])) {
        $email = htmlspecialchars($_POST["signup-email-field"]);
        $password = hash('sha256', htmlspecialchars($_POST["signup-psw-field"]));

        $query = $pdo->prepare("SELECT id FROM tutor WHERE email = ? AND password = ?");
        $query->bind_param('ss', $email, $password);
        if($query->execute()) {
            $result = $query->get_result();
            if($result->num_rows == 1){
                // Existing user
                while ($row = $result->fetch_assoc()) {
                    session_start();
                            
                    // Store data in session variables
                    $_SESSION["tutorid"] = $row["id"];
                    $_SESSION["loggedin"] = true;

                    header("location: index.php");
                }
            }
            else {
                // Not existing user
                // TODO: return no user existing error
                $loginerror = "Username o password non corretti";
            }
        }
        else {
            $loginerror = "Errore interno";
        }
    }
    else {
        $loginerror = "Inserisci tutti i dati richiesti";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
        <title>Tutor Login - TutorMate</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/signup.css?v=2">
        <script src="scripts/signup.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="TutorMate">
        </header>
        <div id="globalcontainer">
            <h1>Accedi su TutorMate</h1>
            <p>Effettua il login utilizzando le tue credenziali da Tutor TutorMate 📚</p>

            <form id="signup-form" method="POST">
                <?php if(strlen($loginerror) > 0) {
                    echo '<div id="loginerror"><p>' . $loginerror . '</p></div>';
                }
                ?>
                <label for="signup-email-field">Email</label>
                <input type="email" name="signup-email-field" id="signup-email-field" placeholder="Inserisci la tua email...">

                <label for="signup-psw-field">Password</label>
                <input type="password" name="signup-psw-field" id="signup-psw-field" placeholder="Inserisci una password...">

                <button type="submit">Accedi</button>
            </form>
        </div>
    </body>
</html>