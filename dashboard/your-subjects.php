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
$servername = "hostingmysql335.register.it";
$usernameD = "Sql1068665";
$password = "3863t3v631";
$dbname = "sql1068665";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    header("location: error.php");
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.png" sizes="32x32">
        <title>Le tue materie - FuoriKLASSE</title>
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
            <h1>Le tue materie</h1>
            <p style="margin-bottom: 5px;">📚 Proprio come a scuola o all'università, le materie sono gli ambiti di studi dei quali vuoi dare ripetizioni. Qui sono elencate tutte le materie delle quali dai ripetizioni. Puoi crearne di nuove oppure modificare quelle esistenti.</p>
            <a href="new-subject.php" style="margin-bottom: 20px; background-color: #0265f9; color: white;" class="comands-btn">Nuova materia</a>
            <?php
            $tutor = $_SESSION["tutorid"];
            $query = $conn->prepare("SELECT * FROM subject WHERE tutor = ? AND status = 0");
            $query->bind_param('i', $tutor);
            if($query->execute()) {
                $result = $query->get_result();
                if($result->num_rows > 0) {
                    // Listing the subjects
                    while ($row = $result->fetch_assoc()) {
                        echo '
                            <div class="subject-box">
                                <p><strong style="font-size: 18px;">' . $row["name"] . '</strong></p>
                                <p><i>' . $row["grade"] . '</i></p>
                                <button class="comands-btn" onclick="deleteSubject(' . $row["id"] . ')">Elimina</button>
                            </div>
                        ';
                    }
                }
            }
            ?>
        </div>
    </body>
</html>