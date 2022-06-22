<?php
error_reporting(-1);
ini_set('display_errors', 'On');

if(isset($_GET["dest"]) && isset($_GET["token"])){
    $token = htmlspecialchars($_GET["token"]);
    if($token === hash("sha256", "!!EP3TutorMatemail")) {
        $dest = htmlspecialchars($_GET["dest"]);
        require 'vendor/autoload.php';

        $transport = (new Swift_SmtpTransport('smtps.aruba.it', 465, 'ssl'))
            ->setUsername('nic@segato.net')
            ->setPassword('Cocco11nov');

        //$transport = new Swift_SendmailTransport('/usr/sbin/sendmail -t');

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('La tua lezione su TutorMate'))
                    ->setFrom(['info@TutorMate.it' => 'TutorMate Team'])
                    ->setTo([$dest])
                    ->setBody('
                    <!DOCTYPE html>
                <html lang="it">
                    <head>
                        <title>Your new reservation: </title>
                        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito" />
                        <meta charset="utf-8">
                        <style>
                            body {
                                align-content: center;
                                font-family: Nunito, Helvetica, sans-serif;
                                width: 90%;
                                margin: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Your new reservation</h1>
                    </body>
                </html>
                    ', 'text/html');

        // Send the message
        if($mailer->send($message)) {
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