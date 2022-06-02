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

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'reepit');

/* Attempt to connect to MySQL database */
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Define variables and initialize with empty values
$name = $grade = "";
$errormsg = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST['subject'])) {

        //collect data
        $error      = null;
        $subject    = htmlspecialchars($_POST['subject']);
        $start      = htmlspecialchars($_POST['startDate']);
        $duration   = htmlspecialchars($_POST['duration']);
        $price      = htmlspecialchars($_POST['price']);
    
        //validation
        if ($subject == '') {
            $error['subject'] = 'Subject is required';
        }
    
        if ($start == '') {
            $error['start'] = 'Start date is required';
        }
    
        if ($duration == '') {
            $duration['end'] = 'Duration is required';
        }
    
        //if there are no errors, carry on
        if (!isset($error)) {
    
            //format date
            $start = date('Y-m-d H:i:s', strtotime($start));
            
            $data['success'] = true;
            $data['message'] = 'Success!';
            
            $query = $conn->prepare("INSERT INTO lesson (subject, datereference, duration, price) VALUES (?, ?, ?, ?)");
            $query->bind_param('isii', $subject, $start, $duration, $price);
            if($query->execute()) {
                // Subject correctly inserted
                header("location: index.php?sm=newlesson");
            }
            else {
                $errormsg = "Errore interno, riprova per favore";
            }
          
        } else {
    
            $data['success'] = false;
            $data['errors'] = $error;
        }
    
        header("location: index.php");
    }
}
else {
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
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
        <title>Nuova lezione - Reepit</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/index.css?v=2">
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="Reepit">
        </header>
        <div id="globalcontainer">
            <h1>Aggiungi una nuova lezione</h1>
            
            <form id="form-infobox" method="POST">
            <?php
            if(count($subject) > 0) {
                echo '
                <label class="control-label" for="subject">Materia</label>
                    <select class="form-control" name="subject" id="subject">
                ';
                foreach($subject as $sub) {
                    echo '<option value="' . $sub["id"] . '">' . $sub["name"] . '</option>';
                }
                echo '</select>
                <label class="control-label" for="startDate">Data di inizio</label>
                <input type="datetime-local" class="form-control" id="startDate" name="startDate" value="' . date("Y-m-d\Th:i", time()) . '" min="' . date("Y-m-d\Th:i", time()) . '">

                <label class="control-label" for="duration">Durata</label>
                <p>Inserisci la durata della lezione in minuti</p>
                <input type="text" class="form-control" name="duration" value="0">

                <label class="control-label" for="price">Prezzo</label>
                <p>Inserisci il prezzo della lezione in centesimi (1565 = 15,65€)</p>
                <input type="text" class="form-control" name="price" value="0">
                
                <div style="margin-top: 20px"></div>
                <button type="button" class="btn btn-secondary" onclick="closeInfoBox()">Annulla</button>
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
    </body>
</html>