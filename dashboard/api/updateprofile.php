<?php
error_reporting(-1);
ini_set('display_errors', 'On');
// Initialize the session
session_start();

// TODO: check if user own the lesson
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    exit;
    die;
}

if(isset($_POST["bio"]) && isset($_POST["vat"]) && isset($_POST["iban"])) {
    $servername = "hostingmysql335.register.it";
    $usernameD = "Sql1068665";
    $password = "3863t3v631";
    $dbname = "sql1068665";

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
        $bio = htmlspecialchars($_POST["bio"]);
        $vat = htmlspecialchars($_POST["vat"]);
        $iban = htmlspecialchars($_POST["iban"]);
        $query = $conn->prepare("UPDATE tutor SET bio = ?, vat = ?, iban = ? WHERE id = ?");
        $query->bind_param('sssi', $bio, $vat, $iban, $tutorid);
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
            $obj->error_msg = "Error updating your profile";
            echo json_encode($obj);
            die;
        }
    }
}