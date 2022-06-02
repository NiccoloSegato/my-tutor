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
if(isset($_SESSION["tutorid"]) && isset($_GET["date"])) {
    $servername = "localhost";
    $usernameD = "root";
    $password = "";
    $dbname = "reepit";

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
        $date = htmlspecialchars($_GET["date"]);
        $query = $conn->prepare("SELECT lesson.id AS lessonid, lesson.subject AS subjectid, subject.name as subjectname, datereference, duration FROM lesson, subject WHERE subject IN (SELECT id FROM subject WHERE tutor = ?) AND datereference >= TIMESTAMP(?) AND datereference <= TIMESTAMP(?) + INTERVAL 30 DAY AND lesson.subject = subject.id AND lesson.status <> 3");
        $query->bind_param('iss', $tutorid, $date, $date);
        $query->execute();
        $result = $query->get_result();
        if($result->num_rows > 0) {
            $lessonsarray = array();
            // Listing the lessons
            while ($row = $result->fetch_assoc()) {
                $res1 = array("id" => 1);
                $temp1 = array("id"=> $row["lessonid"], "subject" => $row["subjectid"], "starting_date" => $row["datereference"], "duration" => $row["duration"], "subject_name" => $row["subjectname"], "reservation" => $res1);
                array_push($lessonsarray, $temp1);
            }
            $obj->lessons_count = $result->num_rows;
            $obj->lessons = $lessonsarray;
            // Adding the section to the final array
            echo json_encode($obj);
        }
        else {
            // No lessons, empty list
            $obj = new \stdClass();
            $obj->error = 0;
            echo json_encode($obj);
            die;
        }
    }
}