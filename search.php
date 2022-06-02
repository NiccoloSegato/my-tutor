<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Cerca un tutor - Reepit</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.ico" sizes="32x32">
        <link rel="stylesheet" href="styles/index.css">
        <link rel="stylesheet" href="styles/search.css">
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="Logo">
            <p style="margin: 0 10px; font-size: 27px;">Reepit</p>
        </header>
        <div id="bodycont">
            <h1 style="font-weight: 700; font-size: 35px; margin: 0;">Trova il tuo tutor</h1>
            <p style="margin-top: 10px">Cerca tra tutti i tutor di Reepit e trova quello che fa per te.</p>
            
            <form method="POST" id="q-box">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["q"])) {
                    // Populate the search bar
                    echo '<input type="text" name="q" id="q" placeholder="Inserisci nome o cognome" value="' . htmlspecialchars($_POST["q"]) . '">';
                }
                else {
                    echo '<input type="text" name="q" id="q" placeholder="Inserisci nome o cognome">';
                }
                ?>
                <button id="q-conf">Cerca</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Search triggered
                if(isset($_POST["q"])) {
                    $requested = htmlspecialchars($_POST["q"]);
                    if(strlen($requested) > 3) {
                        error_reporting(-1);
                        ini_set('display_errors', 'On');

                        $servername = "localhost";
                        $usernameD = "root";
                        $password = "";
                        $dbname = "reepit";

                        $conn = new mysqli($servername, $usernameD, $password, $dbname);
                        $conn->set_charset('utf8mb4');
                        // Check connection
                        if ($conn->connect_error) {
                            header("location: error.php");
                            die;
                        }
                        else {
                            $query = $conn->prepare("SELECT id, name, surname, image FROM tutor WHERE name LIKE ? OR surname LIKE ?");
                            $query->bind_param('ss', $requested, $requested);
                            if($query->execute()) {
                                $result = $query->get_result();
                                if($result->num_rows >= 1){
                                    // Users found
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<a class="search-result" href="tutor.php?id=' . $row["id"] . '">
                                            <img src="' . $row["image"] . '" alt="Tutor image"></img>
                                            <div>
                                                <p style="margin-bottom: 0;">' . $row["name"] . '</p>
                                                <p style="margin-top: 0;"><strong>' . $row["surname"] . '</strong></p>
                                            </div>
                                        </a>
                                        ';
                                    }
                                }
                                else {
                                    echo '<p>😢 Nessun risultato...</p>';
                                }
                            }
                            else {
                                echo '<p>❌ Nessun risultato</p>';
                            }
                        }
                    }
                    else {
                        echo '<p>❌ Sii più specifico...</p>';
                    }
                }
                else {
                    echo '<p>❌ Nessun risultato</p>';
                }
            }
            else {
                echo '<img id="search-img" src="assets/images/search-bg.png" alt="Searching..."></img>';
            }
            ?>

        </div>

        <footer class='footer'>
            <div id='logo-cont'>
                <img src="assets/images/logo.png" width="45" height="45" alt="Reepit logo"></img>
                <div>
                    <h4>Reepit</h4>
                    <p>La rivoluzione dello studio</p>
                </div>
            </div>
            <a href=''>Chi siamo</a>
            <a href=''>Inizia ora</a>
    
            <p style="font-size: 14px; margin-top: 20px;">Copyright 2022 © Reepit - Tutti i diritti riservati</p>
        </footer>
    </body>
</html>