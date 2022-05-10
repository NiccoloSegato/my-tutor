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
$servername = "localhost";
$usernameD = "root";
$password = "";
$dbname = "fuoriklasse_new";

$conn = new mysqli($servername, $usernameD, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    header("location: error.php");
}
else {
    $tutorid = htmlspecialchars($_SESSION["tutorid"]);
    $query = $conn->prepare("SELECT * FROM tutor WHERE id = ?");
    $query->bind_param('i', $tutorid);
    if($query->execute()) {
        $result = $query->get_result();
        if($result->num_rows == 1){
            // Existing user
            while ($row = $result->fetch_assoc()) {
                $tutor_name = $row["name"];
                $tutor_surname = $row["surname"];
                $tutor_bio = $row["bio"];
                $tutor_img = $row["image"];
                $tutor_vat = $row["vat"];
                $tutor_iban = $row["iban"];
            }
        }
        else {
            header("location: missing-tutor.php");
        }
    }
}
?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="assets/images/logo.png" sizes="32x32">
        <title>Il tuo profilo - FuoriKLASSE</title>
        <link rel="stylesheet" href="styles/global.css?v=2">
        <link rel="stylesheet" href="styles/profile.css">
        <script src="scripts/jquery.js"></script>
        <script src="scripts/profile.js"></script>
    </head>
    <body>
        <header>
            <img src="assets/images/logo.png" alt="FuoriKLASSE">
        </header>
        <div id="globalcontainer">
            <h1>Il tuo profilo</h1>
            <p style="margin-bottom: 5px;">👤 Il tuo profilo è la tua vetrina. Assicurati di completarlo in tutte le sue parti e tenerlo sempre aggiornato.</p>
            
            <div class="divider" style="height: 30px;"></div>

            <p><strong>Immagine del profilo</strong></p>
            <form enctype="multipart/form-data" method="POST" id="changeimage-cont">
                <img src="<?php echo $tutor_img ?> " alt="Profile image" id="profileabs">
                <div id="selectzone" style="display: flex; flex-direction: column; justify-content: center;">
                    <label id="file-upload-label" for="file-upload" class="custom-file-upload">Cambia immagine</label>
                    <input id="file-upload" type="file" name="fileToUpload" accept="image/*" onchange="submitImage()"/>
                </div>
            </form>
            <p><strong>Il tuo nome</strong></p>
            <p class="profile-field" id="usernamelbl"><?php echo $tutor_name . ' ' . $tutor_surname ?></p>
            <p style="font-size: 14px !important; margin-top: 5px">Nome e cognome non possono essere cambiati</p>

            <div class="divider" style="height: 15px;"></div>
            <p><strong>Bio</strong></p>
            <textarea id="tutor-bio" name="tutor-bio"><?php echo $tutor_bio ?></textarea>

            <div class="divider" style="height: 15px;"></div>
            <label for="tutor-vat">Codice Fiscale o P.IVA</label>
            <input class="profile-field" style="width: 100%" type="text" id="tutor-vat" name="tutor-vat" value="<?php echo $tutor_vat ?>" >
            <p style="font-size: 14px !important; margin-top: 5px">Questo dato è necessario ai fini fiscali</p>

            <div class="divider" style="height: 15px;"></div>
            <label for="tutor-iban">IBAN</label>
            <input class="profile-field" style="width: 100%" type="text" id="tutor-iban" name="tutor-iban" value="<?php echo $tutor_iban ?>" >
            <p style="font-size: 14px !important; margin-top: 5px">Su questo conto corrente invieremo i prelievi. Assicurati di inserire un conto corrente <strong>intestato a te</strong>.</p>

            <div class="divider" style="height: 30px;"></div>

            <button class="comands-btn" onclick="submitData()">Salva</button>
            <a class="comands-btn" style="color: black; background-color: #d7d7d7;" href="index.php">Annulla</a>
        </div>
    </body>
</html>