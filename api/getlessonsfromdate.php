<?php
error_reporting(-1);
ini_set('display_errors', 'On');

if(isset($_GET["id"]) && isset($_GET["date"])) {
    $servername = "89.46.111.249";
    $usernameD = "Sql1644591";
    $password = "TaPM8fXBfnAsWBA!!";
    $dbname = "Sql1644591_1";

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

        $subjectid = htmlspecialchars($_GET["id"]);
        $date = htmlspecialchars($_GET["date"]);
        $query = $conn->prepare("SELECT * FROM lesson WHERE subject = ? AND DATE(datereference) = ?");
        $query->bind_param('is', $subjectid, $date);
        $query->execute();
        $result = $query->get_result();
        if($result->num_rows > 0) {
            $lessonsarray = array();
            // Listing the lessons
            while ($row = $result->fetch_assoc()) {
                // Check if lesson is reserved
                $lessonid = $row["id"];
                $status = 0;
                $queryi = $conn->prepare("SELECT * FROM reservation WHERE lesson = ? AND status = 1");
                $queryi->bind_param('i', $lessonid);
                $queryi->execute();
                $resulti = $queryi->get_result();
                if($resulti->num_rows > 0) {
                    $status = 1;
                }
                $temp1 = array("id"=> $lessonid, "subject" => $row["subject"], "starting_date" => $row["datereference"], "duration" => $row["duration"], "price" => $row["price"], "status" => $status);
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