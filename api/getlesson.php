<?php
error_reporting(-1);
ini_set('display_errors', 'On');

if(isset($_GET["id"])) {
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

        $subjectid = htmlspecialchars($_GET["id"]);
        $query = $conn->prepare("SELECT lesson.id as lesson_id, tutor.id as teacher_id, tutor.name as teacher_name, tutor.surname as teacher_surname, subject.id as subject_id, subject.name as subject_name, lesson.datereference, lesson.duration, lesson.price, subject.grade FROM lesson, tutor, subject WHERE lesson.id = ? AND subject.tutor = tutor.id AND lesson.subject = subject.id");
        $query->bind_param('i', $subjectid);
        $query->execute();
        $result = $query->get_result();
        if($result->num_rows > 0) {
            // Listing the lessons
            while ($row = $result->fetch_assoc()) {
                $obj->id = $row["lesson_id"];
                $obj->subject = $row["subject_id"];
                $obj->subject_name = $row["subject_name"];
                $obj->grade = $row["grade"];
                $obj->teacher = $row["teacher_id"];
                $obj->teacher_name = $row["teacher_name"] . ' ' . $row["teacher_surname"];
                $obj->starting_date = $row["datereference"];
                $obj->duration = $row["duration"];
                $obj->price = $row["price"];
                // Adding the section to the final array
                echo json_encode($obj);
            }
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