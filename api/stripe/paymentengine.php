<?php
error_reporting(-1);
ini_set('display_errors', 'On');

function generateRandomString($length = 9) {
    return substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

require '../../plugin/vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51L1CLOLQYnoKNATEH0JIrh9piymrLe1lcW0ggODUNu5E2SXxc0eGY4mLwz7AOGRfudx0iTR5uTBkkIUdZLxMivAb00qNJ7va2v');

$YOUR_DOMAIN = 'https://reepit.it';
if(isset($_POST["email"]) && isset($_POST["lessonId"])){

    // Creation of an ID for the order
    $order_id = generateRandomString();

    // Retrieving the lesson ID
    $lessonid = htmlspecialchars($_POST["lessonId"]);

    // Buyer email
    $useremail = htmlspecialchars($_POST["email"]);

    // Connecting to DB
    $servername = "hostingmysql335.register.it";
    $usernameD = "Sql1068665";
    $password = "3863t3v631";
    $dbname = "sql1068665";

    $conn = new mysqli($servername, $usernameD, $password, $dbname);
    $conn->set_charset('utf8mb4');
    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        die;
    }

    // Check if lesson is valid
    $queryl = $conn->prepare("SELECT lesson.price, tutor.name, tutor.id as tutorid FROM lesson, tutor, subject WHERE lesson.id = ? AND subject.tutor = tutor.id AND lesson.subject = subject.id");
    $queryl->bind_param('i', $lessonid);
    $queryl->execute();
    $resultl = $queryl->get_result();
    if($resultl->num_rows == 0){
        // Not existing lesson
        echo json_encode(['error' => 1, 'error_msg' => "Not existing lesson"]);
        die;
    }
    else {
        // Existing lesson, retrieving price
        while ($rowl = $resultl->fetch_assoc()) {
            $lessonprice = $rowl["price"];
            $tutorname = $rowl["name"];
            $tutorid = $rowl["tutorid"];

            // Creating the Stripe payment intent
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => "EUR",
                        'unit_amount' => $lessonprice,
                        'product_data' => [
                            'name' => 'Lezione con ' . $tutorname
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/api/stripe/confirmpayment.php?id=' . $order_id . '&sessionid={CHECKOUT_SESSION_ID}' /*$YOUR_DOMAIN . '/confirmation.html?id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}'*/,
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html'
            ]);

            // Retrieving Stripe variables
            $stripeid = $checkout_session->id;
            $stripeintent = $checkout_session->payment_intent;

            // Calculating commission
            $commission = 0;
            $temp = 0.1 * (int)$lessonprice;
            $commission = intval($temp);

            // Saving pending transaction to DB
            $queryt = $conn->prepare("INSERT INTO transaction (transaction_ref, tutor, lesson, amount, email, commission, transaction_id, transaction_intent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $queryt->bind_param('siiisiss', $order_id, $tutorid, $lessonid, $lessonprice, $useremail, $commission, $stripeid, $stripeintent);
            if($queryt->execute()) {
                // No errors adding the transaction to DB
                $transactionid = $queryt->insert_id;
                
                // Creating the reservation
                $queryr = $conn->prepare("INSERT INTO reservation (lesson, buyer_email, transaction) VALUES (?, ?, ?)");
                $queryr->bind_param('isi', $lessonid, $useremail, $transactionid);
                if($queryr->execute()) {
                    echo json_encode(['id' => $stripeid, 'error' => 0]);
                    die;
                }
                else {
                    echo json_encode(['error' => 1, 'error_msg' => "[12] Error creating the reservation"]);
                    die;
                }

            }
            else {
                echo json_encode(['error' => 1, 'error_msg' => "[11] Error creating the transaction"]);
                die;
            }
        }
    }
}
else {
    http_response_code(401);
    die;
}