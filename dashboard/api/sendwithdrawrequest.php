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

$servername = "89.46.111.38";
$usernameD = "Sql1068665";
$password = "3863t3v631";
$dbname = "Sql1068665_3";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    $obj = new \stdClass();
    $obj->error = 1;
    $obj->error_msg = "CONNECTION ERROR";
    echo json_encode($obj);
    die;
}

// Check if user inserted his IBAN
$tutor = htmlspecialchars($_SESSION["tutorid"]);
$queryiban = $conn->prepare("SELECT iban, vat FROM tutor WHERE id = ?");
$queryiban->bind_param('i', $tutor);
if($queryiban->execute()) {
    $resultiban = $queryiban->get_result();
    if($resultiban->num_rows > 0){
        while ($rowiban = $resultiban->fetch_assoc()) {
            if(is_null($rowiban["iban"]) || is_null($rowiban["vat"])) {
                // User not inserted his IBAN and VAT code
                $obj = new \stdClass();
                $obj->error = 1;
                $obj->error_msg = "Non hai impostato nessun IBAN sul quale versare i fondi. Torna alla dashboard, seleziona \"Il tuo profilo\" e inserisci i dati necessari.";
                echo json_encode($obj);
                die;
            }
            else {
                // Check if user has founds to withdraw
                $query = $conn->prepare("UPDATE transaction SET withdrawn = 1, withdrawn_datereference = CURRENT_TIMESTAMP WHERE tutor = ? AND status = 1 AND withdrawn = 0 AND lesson IN (SELECT id FROM lesson WHERE tutor = ? AND datereference < CURRENT_TIMESTAMP)");
                $query->bind_param('ii', $tutor, $tutor);
                if($query->execute()) {
                    $result = $query->get_result();
                    if($conn->affected_rows > 0){
                        $obj = new \stdClass();
                        $obj->error = 0;
                        echo json_encode($obj);
                        die;
                    }
                    else {
                        $obj = new \stdClass();
                        $obj->error = 1;
                        $obj->error_msg = "Non ci sono fondi da prelevare";
                        echo json_encode($obj);
                        die;
                    }
                }
                else {
                    $obj = new \stdClass();
                        $obj->error = 1;
                        $obj->error_msg = "Internal error";
                        echo json_encode($obj);
                        die;
                }
            }
        }
    }
    else {
        // Probably not existing user
        $obj = new \stdClass();
        $obj->error = 1;
        $obj->error_msg = "User ID error";
        echo json_encode($obj);
        die;
    }
}