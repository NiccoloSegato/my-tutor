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

$subject = [];
$tutor = $_SESSION["tutorid"];
$query = $conn->prepare("SELECT * FROM subject WHERE tutor = ? AND status = 0");
$query->bind_param('i', $tutor);
if($query->execute()) {
    $result = $query->get_result();
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $subject[] = [
                'id'      => $row["id"],
                'name'    => $row["name"],
                'grade'   => $row["grade"]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head lang="it">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Il tuo calendario - FuoriKLASSE</title>
    <link rel="icon" href="http://www.fuoriklasse.com/wp-content/uploads/2020/03/cropped-IMG_2385-32x32.jpg" sizes="32x32">
    <link rel="stylesheet" href="styles/global.css?v=2">
    <link rel="stylesheet" href="styles/calendar.css?v=2">
    <script src="scripts/jquery.js"></script>
</head>
<body>
    <div id="shadow"></div>
    <div id="event-infobox">
        <div style="justify-content: right; display: flex; width: 100%;" onclick="closeInfoBox()"><p id="close-infobox">X</p></div>
        <h3 id="action-title">Nuova lezione</h3>

        <form id="form-infobox" action="api/insert.php" method="POST">
            <?php
            if(count($subject) > 0) {
                echo '
                <label for="subject">Materia</label>
                    <select class="form-control" name="subject" id="subject">
                ';
                foreach($subject as $sub) {
                    echo '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
                }
                echo '</select>
                <label class="control-label" for="startDate">Data di inizio</label>
                <input type="datetime-local" class="form-control" id="startDate" name="startDate" value="' . date("Y-m-d\Th:i", time()) . '" min="' . date("Y-m-d\Th:i", time()) . '">

                <label class="control-label" for="duration">Durata</label>
                <input type="text" class="form-control" name="duration" value="0">

                <label class="control-label" for="price">Prezzo</label>
                <input type="text" class="form-control" name="price" value="0">
                
                <div style="margin-top: 20px"></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="submit" style="background-color: #c9ffc9;" class="btn btn-primary">Pubblica lezione</button>
                ';
            }
            else {
                echo '
                <p><strong>Non hai ancora nessuna materia...</strong></p>
                <p>Crea la tua prima materia e comincia ad aggiungere le tue lezioni</p>
                <a href="new-subject.php" style="background-color: #0265f9; display: block; width: fit-content; margin-top: 10px; color: white; text-decoration: none;" class="btn btn-primary">Nuova materia</a>
                ';
            }
            ?>
        </form>
    </div>
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
        <img src="../assets/images/logo.png" alt="FuoriKLASSE">
    </header>
    <div id="globalcont-cal">
        <h1>📚 Bentornato</h1>
        <p>Gestisci le tue lezioni e visualizza i tuoi guadagni</p>

        <div id="comands-box">
            <div id="gains-box">
                <p id="gains-lbl">0€</p>
                <p style="margin: 0;">Guadagnati questo mese</p>
            </div>
            <div id="comands-holder">
                <button id="addeventbtn" class="comands-btn" onclick="addEvent()">Nuova lezione</button>
                <button class="comands-btn" onclick="openSubjects()">Le tue materie</button>
                <button class="comands-btn" onclick="openGains()">I tuoi guadagni</button>
                <button class="comands-btn">Il tuo profilo</button>
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