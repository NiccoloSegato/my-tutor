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
    $obj = new \stdClass();
    $obj->error = 1;
    $obj->error_msg = "CONNECTION ERROR";
    echo json_encode($obj);
    die;
}

// Check if user has founds to withdraw
$tutor = htmlspecialchars($_SESSION["tutorid"]);
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