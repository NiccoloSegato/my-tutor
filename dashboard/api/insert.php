<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    exit;
    die;
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

if (isset($_POST['subject'])) {

    //collect data
    $error      = null;
    $subject    = $_POST['subject'];
    $start      = $_POST['startDate'];
    $duration   = $_POST['duration'];
    $price      = $_POST['price'];

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

    header("location: ../index.php");
}