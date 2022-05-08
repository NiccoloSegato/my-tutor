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
$query = $conn->prepare("SELECT * FROM subject WHERE tutor = ?");
$query->bind_param('i', $tutor);
if($query->execute()) {
    $result = $query->get_result();
    if($result->num_rows == 1){
        while ($row = $result->fetch_assoc()) {
            $subject[] = [
                'id'      => $row->id,
                'name'    => $row->name,
                'grade'   => $row->grade
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
            <label for="subject">Materia</label>
            <select class="form-control" name="subject" id="subject">
                <?php
                    foreach($subject as $sub) {
                        echo '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
                    }
                ?>
            </select>

            <label class="control-label" for="startDate">Data di inizio</label>
            <input type="datetime-local" class="form-control" id="startDate" name="startDate" value="<?php echo date('Y-m-d\Th:i', time()) ?>" min="<?php echo date('Y-m-d\Th:i', time()) ?>">

            <label class="control-label" for="duration">Durata</label>
            <input type="text" class="form-control" name="duration" value="0">

            <label class="control-label" for="price">Prezzo</label>
            <input type="text" class="form-control" name="price" value="0">
            
            <div style="margin-top: 20px"></div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
            <button type="submit" style="background-color: #c9ffc9;" class="btn btn-primary">Pubblica lezione</button>
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

        <h3>📆 Calendario</h2>
        <!--<div class="modal fade" id="addeventmodal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <form id="createEvent" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="title-group" class="form-group">
                                        <label class="control-label" for="subject">Materia</label>
                                        <select class="form-control" name="subject">-->
                                            <!--<?php/*
                                            foreach($subject as $sub) {
                                                echo '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
                                            }*/
                                            ?>-->
                                        <!-- </select>
                                        errors will go here 
                                    </div>
                                    <div id="startdate-group" class="form-group">
                                        <label class="control-label" for="startDate">Data di inizio</label>
                                        <input type="text" class="form-control datetimepicker" id="startDate" name="startDate">-->
                                        <!-- errors will go here 
                                    </div>
                                    <div id="enddate-group" class="form-group">
                                        <label class="control-label" for="duration">Durata</label>
                                        <input type="text" class="form-control" name="duration">-->
                                        <!-- errors will go here 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="color-group" class="form-group">
                                        <label class="control-label" for="price">Prezzo</label>
                                        <input type="text" class="form-control" name="price" value="0">-->
                                        <!-- errors will go here 
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    </form>-->

                <!--</div> /.modal-content -->
            <!--</div> /.modal-dialog -->
        <!--</div> /.modal 
        <div class="modal fade" id="editeventmodal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="font-weight: bold;">Il tuo evento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div>
                                    <h5>Prenotazione</h5>
                                    <div id="prenotazione-box">
                                        <p>Ancora nessuna prenotazione</p>
                                    </div>
                                </div>
                            </div>
                            <form id="editEvent" class="form-horizontal">
                            <input type="hidden" id="editEventId" name="editEventId" value="">
                            <h5>Modifica evento</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="edit-title-group" class="form-group">
                                        <label class="control-label" for="editEventSubject">Materia</label>
                                        <input type="text" class="form-control" id="editEventSubject" name="editEventSubject">-->
                                        <!-- errors will go here 
                                    </div>
                                    <div id="edit-startdate-group" class="form-group">
                                        <label class="control-label" for="editStartDate">Data di inizio</label>
                                        <input type="text" class="form-control datetimepicker" id="editStartDate" name="editStartDate">-->
                                        <!-- errors will go here 
                                    </div>
                                    <div id="edit-enddate-group" class="form-group">
                                        <label class="control-label" for="editDuration">Durata</label>
                                        <input type="text" class="form-control" id="editDuration" name="editDuration">-->
                                        <!-- errors will go here 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="edit-color-group" class="form-group">
                                        <label class="control-label" for="editPrice">Prezzo</label>
                                        <input type="text" class="form-control" id="editPrice" name="editPrice">-->
                                        <!-- errors will go here 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary">Salva le modifiche</button>
                    <button type="button" class="btn btn-danger" id="deleteEvent" data-id>Elimina</button>
                    </div>
                    </form>-->

                <!--</div> /.modal-content -->
            <!--</div> /.modal-dialog -->
        <!--</div> /.modal 
        <div class="container">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addeventmodal">
            Add Event
            </button>
            <div id="calendar"></div>
        </div>-->
        <button id="addeventbtn" onclick="addEvent()">Nuovo evento</button>
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