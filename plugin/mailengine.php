<?php
error_reporting(-1);
ini_set('display_errors', 'On');

if(isset($_GET["dest"]) && isset($_GET["token"]) && isset($_GET["lesson"])){
    $token = htmlspecialchars($_GET["token"]);
    $lesson = htmlspecialchars($_GET["lesson"]);
    if($token === hash("sha256", "!!EP3TutorMatemail")) {
        $dest = htmlspecialchars($_GET["dest"]);

        // Retrieving infos from lesson
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

        $queryl = $conn->prepare("SELECT tutor.email as tutormail, tutor.name as tutorname, tutor.surname, subject.name as subjectname, lesson.datereference, lesson.duration FROM lesson, tutor, subject WHERE lesson.id = ? AND subject.tutor = tutor.id AND lesson.subject = subject.id");
        $queryl->bind_param('i', $lesson);
        $queryl->execute();
        $resultl = $queryl->get_result();
        if($resultl->num_rows == 0){
            // Not existing lesson, not sending mail
            echo json_encode(['error' => 1, 'error_msg' => "Not existing lesson"]);
            die;
        }
        else {
            // Existing lesson, retrieving price
            while ($rowl = $resultl->fetch_assoc()) {
                $tutorname = $rowl["tutorname"] . ' ' . $rowl["surname"];
                $subjectname = $rowl["subjectname"];
                $startingdate = $rowl["datereference"];
                $duration = $rowl["duration"];
                $tutormail = $rowl["tutormail"];
            }
        }

        require 'vendor/autoload.php';

        $transport = (new Swift_SmtpTransport('smtps.aruba.it', 465, 'ssl'))
            ->setUsername('nic@segato.net')
            ->setPassword('Cocco11nov');

        //$transport = new Swift_SendmailTransport('/usr/sbin/sendmail -t');

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('La tua lezione con ' . $tutorname))
                    ->setFrom(['info@TutorMate.it' => 'TutorMate Team'])
                    ->setTo([$dest])
                    ->setBody('
                    <html lang="it">
                    <html>
                        <head>
                            <title>La tua lezione con ' . $tutorname . '</title>
                            <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito" />
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            <style>
                                body {
                                    text-align: center;
                                    font-family: Nunito, Helvetica, sans-serif;
                                    width: 90%;
                                    margin: 30px auto;
                                    background-color: #f9f9f9;
                                }
                                .divider {
                                    background-color: rgb(220, 220, 220);
                                    height: 2px;
                                    width: 100%;
                                    margin: 10px 0;
                                }
                                #res-card {
                                    padding: 10px;
                                    width: calc(100% - 20px);
                                    border-radius: 10px;
                                    background-color: white;
                                }
                                #res-card p, #res-card h2 {
                                    margin: 5px;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="https://tutormate.it/assets/images/logo.png" alt="TutorMate Logo" width="50" height="50">
                            <h1>🚀 Lezione confermata!</h1>
                            <p>Grazie per aver prenotato la tua lezione con <strong>TutorMate</strong> 🥳</p>
                            <p>Ecco un riepilogo della tua prenotazione</p>
                            <div id="res-card">
                                <h2>' . $subjectname . '</h2>
                                <p>Il tuo tutor sarà <strong>' . $tutorname . '</strong></p>
                                <p>Inizierà il <strong>' . $startingdate . '</strong></p>
                                <p>Durata: <strong>' . $duration . ' minuti</strong></p>
                            </div>
                            <p>Abbiamo ricevuto il tuo pagamento, il tutor ti contatterà per concordare le modalità e le informazioni utili per la lezione.</p>
                            <div class="divider"></div>
                            <p style="margin-bottom: 30px;">Grazie mille e buona lezione con TutorMate!</p>
                        </body>
                    </html>
                    ', 'text/html');

        $messagetutor = (new Swift_Message('Nuova prenotazione da ' . $dest))
        ->setFrom(['info@TutorMate.it' => 'TutorMate Team'])
        ->setTo([$dest])
        ->setBody('
        <html lang="it">
        <html>
            <head>
                <title>Nuova prenotazione da ' . $dest . '</title>
                <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito" />
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <style>
                    body {
                        text-align: center;
                        font-family: Nunito, Helvetica, sans-serif;
                        width: 90%;
                        margin: 30px auto;
                        background-color: #f9f9f9;
                    }
                    .divider {
                        background-color: rgb(220, 220, 220);
                        height: 2px;
                        width: 100%;
                        margin: 10px 0;
                    }
                    #res-card {
                        padding: 10px;
                        width: calc(100% - 20px);
                        border-radius: 10px;
                        background-color: white;
                    }
                    #res-card p, #res-card h2 {
                        margin: 5px;
                    }
                </style>
            </head>
            <body>
                <img src="https://tutormate.it/assets/images/logo.png" alt="TutorMate Logo" width="50" height="50">
                <h1>🚀 Lezione prenotata!</h1>
                <p>Una tua lezione è stata prenotata su <strong>TutorMate</strong> 🥳</p>
                <p>Ecco un riepilogo della prenotazione che è stata effettuata</p>
                <div id="res-card">
                    <h2>' . $subjectname . '</h2>
                    <p>Inizierà il <strong>' . $startingdate . '</strong></p>
                    <p>Durata: <strong>' . $duration . ' minuti</strong></p>
                </div>
                <p>Vai alla tua <a href="https://tutormate.it/dashboard/">dashboard</a> per visualizzare il numero di telefono dello studente e i dettagli da lui inseriti</p>
                <p>Abbiamo ricevuto il pagamento, lo puoi visualizzare nella tua dashboard e sarà disponibile per il prelievo una volta completata la lezione.</p>
                <div class="divider"></div>
                <p style="margin-bottom: 30px;">Grazie mille e buona lezione con TutorMate!</p>
            </body>
        </html>
        ', 'text/html');

        // Send the message
        if($mailer->send($message) && $mailer->send($messagetutor)) {
            echo json_encode(['status' => '1', 'dest' => $dest]);
            die;
        }
        else {
            echo json_encode(['status' => '2', 'dest' => $dest]);
            die;
        }
    }
    else {
        error_log("Error sending email!, wrong token", 0);
        echo "ERROR 1";
    }
}
else {
    error_log("Error sending email!", 0);
    echo "ERROR 2";
}