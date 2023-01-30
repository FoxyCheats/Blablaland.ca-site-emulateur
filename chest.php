<?php
session_start();

include_once('backend/database.php');
include_once('backend/usercontroller.php');
include_once('backend/api.php');


$logged = isset($_SESSION['api_token']);

if (!$logged) {
    header("location: /signin.php");
}

$account = getAccount($pdo, $_SESSION['api_token']);

if ((time() - $account['last_chest']) >= 21600) {
    $gifts = array(
        "5" => "0",
        "25" => "5",
        "60" => "10",
        "70" => "20",
        "80" => "25",
        "85" => "50",
        "90" => "100",
        "99" => "200"
    );

    $bbl = 0;

    $luck = rand(0, 100);
    if ($luck == 100) {
        $bbl = 10000;
    } else if ($luck <= 5) {
        $bbl = 0;
    } else if ($luck <= 25) {
        $bbl = 5;
    } else if ($luck <= 60) {
        $bbl = 10;
    } else if ($luck <= 70) {
        $bbl = 20;
    } else if ($luck <= 80) {
        $bbl = 25;
    } else if ($luck <= 85) {
        $bbl = 50;
    } else if ($luck <= 90) {
        $bbl = 100;
    } else if ($luck <= 99) {
        $bbl = 200;
    }

    $account['bbl'] += $bbl;
    $gift = "$bbl BBL";
    $req = $pdo->prepare("UPDATE users SET last_chest=?, bbl=? WHERE api_token=?");
    $req->execute([time(), $account['bbl'], $account['api_token']]);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>

    <title>Blablaland - Coffre</title>
</head>

<body>
    <?php include_once("header.php"); ?>
    <audio src="/data/sfx/open_chest.mp3" id="chest_sfx"></audio>

    <div class="container" style="width:800px;">
        <div class="jumbotron">
            <div style="width:600px;margin:0 auto;">
                <center>
                    <h3>Coffre</h3>
                    <hr style="margin-bottom:5px;">
                </center>
            </div><br>
            <center>
                <div id="div-chest">
                    <i>Tu peux récupérer un coffre toutes les 6 heures minimum, alors profites-en bien !<br>
                        De plus, il parait que tu peux gagner 10 000 blabillons ! Mais tu peux aussi ne pas en gagner ! <img src='frontend/img/vempire.svg'></i><br>
                    Dernier coffre pris le <?php echo date("d/m/Y à H:i:s", $account["last_chest"]); ?><br>
                    <img src="frontend/img/chest.png" id="chest-image" width="200">
                </div>

                <?php if (isset($gift)) { ?>
                    <p id="bbl-anouncement" style="visibility: hidden;">Tu viens de gagner <?= $gift ?> !</p>
                    <script src="frontend/js/chest_opening.js"></script>
                <?php } ?>
            </center>
        </div>
    </div>
    <br><br>
    <?php
    include_once('footer.php');
    ?>
</body>

</html>