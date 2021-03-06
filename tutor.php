<?php
if(!isset($_GET["id"])) {
    header("location: missing-tutor.php");
}

error_reporting(-1);
ini_set('display_errors', 'On');

$servername = "89.46.111.249";
$usernameD = "Sql1644591";
$password = "TaPM8fXBfnAsWBA!!";
$dbname = "Sql1644591_1";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    header("location: error.php");
}
else {
    $tutorid = htmlspecialchars($_GET["id"]);
    $query = $conn->prepare("SELECT * FROM tutor WHERE id = ?");
    $query->bind_param('i', $tutorid);
    if($query->execute()) {
        $result = $query->get_result();
        if($result->num_rows == 1){
            // Existing user
            while ($row = $result->fetch_assoc()) {
                $tutor_name = $row["name"];
                $tutor_surname = $row["surname"];
                $tutor_bio = $row["bio"];
                $tutor_img = $row["image"];
            }
        }
        else {
            header("location: missing-tutor.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
        <title>Prenota una lezione con <?php echo $tutor_name ?> <?php echo $tutor_surname ?> - TutorMate</title>
        <link rel="stylesheet" href="styles/tutor.css">
        <script src="https://js.stripe.com/v3/"></script>
        <script src="scripts/jquery.js"></script>
        <script src="scripts/tutor.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.ico" alt="Logo">
            <p style="margin: 0 10px; font-size: 27px;">TutorMate</p>
        </header>
        <div id="bodycont">
            <div id="tutor-profile">
                <img id="tutor-profile-img" src="<?php echo $tutor_img ?>" alt="Tutor Image">
                <div id="tutor-name-box">
                    <h2><?php echo $tutor_name ?> <?php echo $tutor_surname ?></h2>
                    <p><?php echo $tutor_bio ?></p>
                </div>
            </div>

            <div id="globalcontainer">
            <div style="display: block;" id="subjects-global">
                <h2 style="margin-bottom: 0; color: #0366fa;">Seleziona una materia</h2>
                <div id="subjects-list">
                    <?php
                    $query2 = $conn->prepare("SELECT * FROM subject WHERE tutor = ? AND status = 0");
                    $query2->bind_param('i', $tutorid);
                    if($query2->execute()) {
                        $result2 = $query2->get_result();
                        if($result2->num_rows > 0) {
                            while ($row2 = $result2->fetch_assoc()) {
                                echo '
                                    <div class="subject-div" onclick="selectSubject(' . $row2["id"] . ')">
                                        <p style="font-weight: bold; font-size: 18px; line-height: 1;">' . $row2["name"] . '</p>
                                        <p style="color: grey;">' . $row2["grade"] . '</p>
                                    </div>
                                ';
                            }
                        }
                        else {
                            echo '<p>Nessuna materia</p>';
                        }
                    }
                    ?>
                </div>
                <div id="calendar-box">
                    <h2 style="color: #0366fa; margin-bottom: 10px;">Seleziona una data</h2>
                    <div id="calbox" class="container col-sm-4 col-md-7 col-lg-4 mt-5">
                        <div class="card">
                            <h3 class="card-header" id="monthAndYear"></h3>
                            <table class="table table-bordered table-responsive-sm" id="calendar">
                                <thead>
                                <tr>
                                    <th>Dom</th>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mer</th>
                                    <th>Gio</th>
                                    <th>Ven</th>
                                    <th>Sab</th>
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
                    <script src="scripts/tutor-calendar.js"></script>
                    <div id="date-box">
                        <h2 style="color: #0366fa; margin-bottom: 0;">Seleziona un orario</h2>
                        <div id="date-selector">
                            <div class="date-obj">
                                <p>Nessuna data selezionata</p>
                            </div>
                        </div>
                    </div>
                    <div id="summary-slot">
                        <h2 style="color: #0366fa; margin-bottom: 5px;">Checkout</h2>
                        <div id="summary-box">
                            <p id="sum-lesson-name">-</p>
                            <p id="sum-grade-name">-</p>
                            <p id="sum-date-name">-</p>
                            <p id="sum-duration-name">-</p>
                            <p id="sum-total-price">-</p>

                            <div class="sum-divider"></div>

                            <p style="font-weight: bold; margin-bottom: 10px;">Informazioni richieste</p>

                            <label for="sh-email">La tua email</label>
                            <input type="email" name="sh-email" id="sh-email" placeholder="Inserisci la tua mail">

                            <label for="sh-phone">Il tuo numero di telefono</label>
                            <input type="phone" name="sh-phone" id="sh-phone" placeholder="Inserisci il tuo numero di telefono">

                            <label for="sh-description">Argomenti</label>
                            <textarea id="sh-description" name="sh-description" placeholder="Descrivi gli argomenti che ti piacerebbe trattare e altre info utili"></textarea>
                            
                            <button id="sum-confirm-btn">Prenota e vai al pagamento</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>