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
$servername = "89.46.111.249";
$usernameD = "Sql1644591";
$password = "TaPM8fXBfnAsWBA!!";
$dbname = "Sql1644591_1";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    header("location: error.php");
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
        <title>I tuoi guadagni - TutorMate</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/gains.css">
        <script src="scripts/jquery.js"></script>
        <script src="scripts/gains.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="TutorMate">
        </header>
        <div id="globalcontainer">
            <h1>I tuoi guadagni</h1>
            <p style="margin-bottom: 5px;">💰 Qui puoi visualizzare le transazioni che le persone hanno fatto verso di te, controllare i tuoi guadagni e richiedere il prelivo verso il tuo conto</p>
            
            <?php
            $tutor = $_SESSION["tutorid"];
            $query = $conn->prepare("SELECT transaction.id as transactionid, subject.name as subjectname, lesson.datereference as lessondate, lesson.duration as lessonduration, transaction.email as transactionemail, transaction.withdrawn as withdrawn, withdrawn_datereference, transaction.amount as transactionamount, transaction.commission FROM transaction, lesson, subject WHERE transaction.tutor = ? AND subject.tutor = transaction.tutor AND lesson.id = transaction.lesson AND lesson.subject = subject.id AND transaction.status = 1 LIMIT 20");
            $queryt = $conn->prepare("SELECT sum(transaction.amount - transaction.commission) as total FROM transaction, lesson WHERE tutor = ? AND transaction.status = 1 AND withdrawn = 0 AND lesson.id = transaction.lesson AND lesson.datereference < (CURRENT_TIMESTAMP)");
            $query->bind_param('i', $tutor);
            $queryt->bind_param('i', $tutor);
            if($queryt->execute()) {
                $resultt = $queryt->get_result();
                if($resultt->num_rows > 0){
                    while ($rowt = $resultt->fetch_assoc()) {
                        if(is_null($rowt["total"])) {
                            // No transaction with status 1
                            echo '<div id="comands-box">
                                <p id="gains-lbl">0€</p>
                                <p>Disponibili per il prelievo</p>
                                </div>
                            ';
                        }
                        else {
                            echo '<div id="comands-box">
                                    <p id="gains-lbl">' . number_format($rowt["total"] / 100, 2) . '€</p>
                                    <p>Disponibili per il prelievo</p>
                                    <button onclick="checkWithdraw()" style="background-color: #0265f9; color: white; margin-top: 10px;" class="btn">Preleva i fondi</button>
                            </div>
                            ';
                        } 
                    }
                }
                else {
                    echo '<div id="comands-box">
                                <p id="gains-lbl">0€</p>
                                <p>Disponibili per il prelievo</p>
                        </div>
                    ';
                }
            }
            ?>

            <h2>Storico transazioni</h2>
            <?php
            if($query->execute()) {
                $result = $query->get_result();
                if($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="transaction-box">
                            <p style="font-size: 14px;color: #626262;">ID transazione: ' . $row["transactionid"] . '</p>
                            <p style="font-size: 20px;font-weight: 700;">Lezione di ' . $row["subjectname"] . ' </p>
                            <p style="color: #4a4a4a;">Del ' . substr($row["lessondate"], 0, 10) . ' alle ' . substr($row["lessondate"], 10) . ', durata '  . $row["lessonduration"] . ' minuti</p>
                            <p style="font-weight: 700;">Prenotato da ' . $row["transactionemail"] . '</p>
                            <div style="background-color: #d9d9d9; width: 100%; height: 2px; margin: 5px 0;"></div>
                            <p style="color: #00bb56; font-weight: 700; font-size: 18px;">+' . number_format(($row["transactionamount"] - $row["commission"]) / 100, 2) . '€</p>
                            <p style="font-size: 14px;">' . number_format($row["transactionamount"] / 100, 2) . '€ - '. number_format($row["commission"] / 100, 2) . '€ di commissioni di servizio</p>
                            ';
                            if($row["withdrawn"] == 1) {
                                echo '<p><strong class="with-pend">IN ATTESA</strong>Fondi richiesti il ' . $row["withdrawn_datereference"] . ', arriveranno sul tuo conto a breve</p>';
                            }
                            else if ($row["withdrawn"] == 2) {
                                echo '<p><strong class="with-comp">PRELEVATI</strong>Fondi prelevati il ' . $row["withdrawn_datereference"] . '</p>';
                            }
                            echo '
                        </div>
                        ';
                    }
                }
                else {
                    echo '<p>Ancora nessuna transazione...</p>';
                }
            }
            ?>
        </div>
    </body>
</html>