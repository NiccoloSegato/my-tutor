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
<html>
<head lang="it">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Il tuo calendario - Reepit</title>
    <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
    <link rel="stylesheet" href="styles/global.css?v=2">
    <link rel="stylesheet" href="styles/calendar.css?v=2">
    <script src="scripts/jquery.js"></script>
</head>
<body>
    <div id="shadow"></div>
    <div id="exist-infobox">
        <div style="justify-content: right; display: flex; width: 100%;" onclick="closeInfoBox()"><p id="close-infobox">X</p></div>
        <h3 id="action-title">Informazioni lezione</h3>

        <p id="ex-subject-name"></p>
        <p id="ex-date-dur"></p>
        <p id="ex-price"></p>

        <div style="background-color: #e3e3e3; height: 2px; width: 100%; margin-top: 20px; margin-bottom: 30px;"></div>

        <h3>Prenotazione</h3>
        <p id="ex-res-name"></p>

        <div style="width: 100%; margin-top: 20px; margin-bottom: 30px;"></div>

        <button class="btn btn-secondary" data-dismiss="modal" onclick="closeInfoBox()">Chiudi</button>
        <button style="background-color: #ffa0a0;" class="btn btn-primary" id="del-les">Elimina lezione</button>
    </div>
    <header>
        <img src="../assets/images/logo.png" alt="Reepit">
    </header>
    <div id="globalcont-cal">
        <h1>📚 Bentornato</h1>
        <p>Gestisci le tue lezioni e visualizza i tuoi guadagni</p>

        <div id="comands-box">
            <div id="gains-box">
                <p id="gains-lbl">
                    <?php
                    $queryt = $conn->prepare("SELECT SUM(amount - commission) AS total FROM transaction WHERE tutor = ? AND status = 1 AND datereference >= NOW() - INTERVAL 30 DAY");
                    $queryt->bind_param('i', $tutor);
                    if($queryt->execute()) {
                        $resultt = $queryt->get_result();
                        if($resultt->num_rows > 0){
                            while ($rowt = $resultt->fetch_assoc()) {
                                echo number_format($rowt["total"] / 100, 2);
                            }
                        }
                    }
                    ?>
                €</p>
                <p style="margin: 0;">Guadagnati questo mese</p>
            </div>
            <div id="comands-holder">
                <button id="addeventbtn" class="comands-btn" onclick="goToNewLesson()">Nuova lezione</button>
                <button class="comands-btn" onclick="openSubjects()">Le tue materie</button>
                <button class="comands-btn" onclick="openGains()">I tuoi guadagni</button>
                <button class="comands-btn" onclick="openProfile()">Il tuo profilo</button>
            </div>
        </div>

        <h2>📆 Calendario</h2>
        <div id="calbox" class="container col-sm-4 col-md-7 col-lg-4 mt-5">
            <div class="card">
                <h3 class="card-header" id="monthAndYear"></h3>
                <table class="table table-bordered table-responsive-sm" id="calendar">
                    <thead>
                        <tr>
                            <th>Domenica</th>
                            <th>Lunedì</th>
                            <th>Martedì</th>
                            <th>Mercoledì</th>
                            <th>Giovedì</th>
                            <th>Venerdì</th>
                            <th>Sabato</th>
                        </tr>
                    </thead>

                    <tbody id="calendar-body">

                    </tbody>
                </table>

                <div class="form-inline">
                    <button class="btn btn-outline-primary col-sm-6" id="previous" onclick="previous()">Precedente</button>
                    <button class="btn btn-outline-primary col-sm-6" id="next" onclick="next()">Successivo</button>
                </div>
            </div>
        </div>
        <script src="scripts/dashboard-cal.js"></script>

    </div>

</body>
</html>