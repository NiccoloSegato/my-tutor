<?php
error_reporting(-1);
ini_set('display_errors', 'On');
if(isset($_GET["sessionid"]) && isset($_GET["id"])){
    $sessionid = htmlspecialchars($_GET["sessionid"]);
    $order_id = htmlspecialchars($_GET["id"]);

    $url = 'https://api.stripe.com/v1/checkout/sessions/' . $sessionid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST,false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_VERBOSE,true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer sk_test_51L1CLOLQYnoKNATEH0JIrh9piymrLe1lcW0ggODUNu5E2SXxc0eGY4mLwz7AOGRfudx0iTR5uTBkkIUdZLxMivAb00qNJ7va2v'
    ));

    $result = curl_exec($ch);
    if(curl_errno($ch)) {
        http_response_code(500);
        die;
    }
    curl_close($ch);
    $response = json_decode($result, true);
    if($response["payment_status"] == "paid") {
        // Payment completed
        $servername = "89.46.111.249";
        $usernameD = "Sql1644591";
        $password = "TaPM8fXBfnAsWBA!!";
        $dbname = "Sql1644591_1";

        $conn = new mysqli($servername, $usernameD, $password, $dbname);
        $conn->set_charset('utf8mb4');
        // Check connection
        if ($conn->connect_error) {
            http_response_code(500);
            die;
        }

        // Updating transaction status
        $queryt = $conn->prepare("UPDATE transaction SET status = 1 WHERE transaction_ref = ?");
        $queryt->bind_param('s', $order_id);
        $queryt->execute();
        if($queryt->affected_rows > 0) {
            // Update reservation status
            $queryr = $conn->prepare("UPDATE reservation SET status = 1 WHERE transaction IN (SELECT id FROM transaction WHERE transaction_ref = ? AND status = 1)");
            $queryr->bind_param('s', $order_id);
            $queryr->execute();
            if($queryr->affected_rows > 0) {
                header('location: /confirmation.php?id=' . $order_id);
                die;
            }
            else {
                // TODO: handle the error
                http_response_code(600);
            }
        }
        else {
            // TODO: handle the error
            http_response_code(600);
        }
    }
}
else {
    http_response_code(400);
    die;
}