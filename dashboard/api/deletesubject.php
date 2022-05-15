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

if(isset($_POST["id"])) {
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
    else {
        $obj = new \stdClass();
        $obj->error = 0;

        $tutorid = htmlspecialchars($_SESSION["tutorid"]);
        $lessonid = htmlspecialchars($_POST["id"]);
        $query = $conn->prepare("UPDATE subject SET status = 3 WHERE id = ? AND tutor = ?");
        $query->bind_param('ii', $lessonid, $tutorid);
        if($query->execute()) {
            $obj = new \stdClass();
            $obj->error = 0;
            echo json_encode($obj);
            die;
        }
        else {
            // Some error occurred
            $obj = new \stdClass();
            $obj->error = 1;
            $obj->error_msg = "Error removing the lesson";
            echo json_encode($obj);
            die;
        }
    }
}