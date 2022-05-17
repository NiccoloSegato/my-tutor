<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    exit;
    die;
}

function get_client_ip_local() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf'); // valid extensions
    $final_path = $_SERVER['DOCUMENT_Sql1068665'] . "/mytutor/uploads/user/";

    if(isset($_FILES["fileToUpload"])){
        if($_FILES['fileToUpload']) {
            $img = $_FILES['fileToUpload']['name'];
            $tmp = $_FILES['fileToUpload']['tmp_name'];
            // get uploaded file's extension
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

            $tempToken = hash('sha256',get_client_ip_local() . date("h:i:sa") . '!EP3');

            if(in_array($ext, $valid_extensions)) {
                $final_image = $tempToken . ".png";
                $final_path = $final_path . $final_image;
                if(move_uploaded_file($tmp, $final_path)) {
                    // Image uploaded, saving on the DB
                    $servername = "hostingmysql335.register.it";
                    $usernameD = "Sql1068665";
                    $password = "3863t3v631";
                    $dbname = "sql1068665";

                    $conn = new mysqli($servername, $usernameD, $password, $dbname);
                    $conn->set_charset('utf8mb4');
                    // Check connection
                    if ($conn->connect_error) {
                        http_response_code(500);
                    }
                    $finalpath = "uploads/user/" . $tempToken . ".png";
                    $tutorid = htmlspecialchars($_SESSION["tutorid"]);
                    $queryp = $conn->prepare("UPDATE tutor SET image = ? WHERE id = ?");
                    $queryp->bind_param('si', $finalpath, $tutorid);
                    $queryp->execute();
                    
                    $obj = new \stdClass();
                    $obj->error = 0;
                    $obj->token = $tempToken;
                    echo json_encode($obj);
                    die;
                }
                else {
                    $obj = new \stdClass();
                    $obj->error = 1;
                    $obj->error_code = 4;
                    $obj->error_msg = "Error uploading your image";
                    echo json_encode($obj);
                    die;
                }
            }
            else {
                $obj = new \stdClass();
                $obj->error = 1;
                $obj->error_code = 3;
                $obj->error_msg = "Image extension not supported";
                echo json_encode($obj);
                die;
            }
        }
        else {
            $obj = new \stdClass();
            $obj->error = 1;
            $obj->error_code = 2;
            $obj->error_msg = "Missing image param";
            echo json_encode($obj);
            die;
        }
    }
    else {
        $obj = new \stdClass();
        $obj->error = 1;
        $obj->error_code = 1;
        $obj->error_msg = "Missing image param";
        echo json_encode($obj);
        die;
    }
}
else {
    http_response_code(501);
    die;
}