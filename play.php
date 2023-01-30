<?php
session_start();

include_once('backend/database.php');
include_once('backend/api.php');
include_once('backend/usercontroller.php');


if (!isset($_SESSION['api_token'])) {
    return header("location: /signin.php");
}

$ip = hash("sha512", getIp());
$bans = $pdo->prepare("SELECT * FROM banip WHERE ip = ?");
$bans->execute(array($ip));
$banned = $bans->rowCount();
$error = "";
$account = getAccount($pdo, $_SESSION['api_token']);
if ($account['email_verified'] == "0") {
    $error = "Vous devez vérifier votre email puis vous connecter pour accéder au jeu !";
} else {
    $logged = true;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    include_once("head.php");
    ?>
    <title>Blablaland - Jouer</title>
</head>

<body>
    <?php
    include_once("header.php");
    if (!empty($error)) { ?>
        <div class="errors bg-danger alert">
            <ul>
                <?php echo $error; ?>
            </ul>
        </div>
    <?php } else {
    ?>
        <div style="width: 950px;margin:0 auto;">
            <span style="float:right;font-size:15px;margin-top:7px;">
                <?php
                if (!$logged) {
                ?> <i class="fa fa-long-arrow-right"></i>
                    <a href="inscription.php">Inscris-toi</a> ou <a href="connexion.php">connecte-toi</a> pour pouvoir profiter pleinement du tchat.
                <?php
                } else {
                    $bbl = $account["bbl"];
                    $xp = $account["xp"];
                   // echo "<button type=\"button\" class=\"btn btn-primary\" style=\"background: #1D2E42; width: 200px; margin-right: 10px; \" id=\"BBLLIGHTMODE\"><img src=\"frontend/img/nightmode.png\"> éteindre la lumière</button>"; // Lumiere
                    echo "<button type=\"button\" class=\"btn btn-primary\" style=\"background: #fc01ca; width: 200px; margin-right: 10px; \" id=\"BBLINFOS_BBL2\"><img src=\"frontend/img/picto_blabillons.png\"> $bbl BBL</button>";
                    echo "<button type=\"button\" class=\"btn btn-primary\" style=\"background: #2096f2; width: 200px; margin-right: 10px; \" id=\"BBLINFOS_XP2\"><img src=\"frontend/img/picto_xp.png\"> $xp XP</button>";
                    echo "<button type=\"button\" class=\"btn btn-primary\" style=\"background: #04b40a; width: 200px; margin-right: 10px; \" id=\"BBLINFOS_AMIS2\"><img src=\"frontend/img/picto_amis.png\">" . strval(count(explode(',', $account['amis'])) - 1) . " AMIS</button>";
                }
                ?>
            </span>
        </div>
        <div class="container jumbotron">
            <br>
            <center>
                <div id="prechat" class="flash chat" style="border-radius: 10px;">
                    <div id="chat" style="visibility: visible;">
                    </div>
                    <div style="background-color:#FFFFFF;padding:20px;text-align: left" id="flash-warn"> Afin de profiter pleinement de Blablaland,
                        <strong>Flash Player 10 <em>minimum</em></strong> doit être installé.<br> Flash
                        Player
                        est un petit logiciel qui permet de lire le contenu Flash sur Internet. Blablaland utilise cette
                        technologie afin de vous offrir du contenu de qualité. <br> <br> <strong>Installer Flash
                            Player.</strong><br> Cette opération est sans aucun risque, certifié par <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash&amp;Lang=French" target="_blank" class="fofo_link_extern">Adobe</a><br> <br><br> <img src="frontend/img/fleche_violet.gif"> <strong>Installer
                            Flash
                            Player</strong> <br><br> <a href="https://get.adobe.com/flashplayer/" target="_blank" class="fofo_link_extern"> <img src="frontend/img/get_flash_player.jpg" border="0" style="padding-bottom:5px;"><br><strong>Cliquez pour installer le Player Flash</strong>
                        </a></div>
                </div>
            </center>
            <script>
                swfobject.embedSWF("swfs/chat.swf?CACHE_VERSION=473&time=<?= time(); ?>",
                    "chat",
                    "950",
                    "560",
                    "11.0",
                    "swfs/expressInstall.swf", {
                        FBFROMAPP: 0,
                        CACHE_VERSION: 473,
                        FBAPPID: "0",
                        SESSION: "<?= isset($_SESSION['session']) ? $_SESSION['session'] : 0; ?>",
                        DAILYMSG: "Si+tu+as+des+probl%E8mes+de+performance%2C+tu+peux+baisser+la+qualit%E9+du+jeu+depuis+%26quot%3BMenu+%26gt%3B+R%E9glages%26quot%3B",
                        DAILYMSGSECU: "Un+vrai+mod%E9rateur+ne+te+demandera+jamais+ton+mot+de+passe.+Ne+le+communique+donc+%E0+personne+%21"
                    }, {
                        wmode: "transparent"
                    }, {
                        name: "chat"
                    }
                );
            </script>
        </div>

        <script>
            function isFlashEnabled() {
                var hasFlash = false
                try {
                    var activated = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                    if (activated) hasFlash = true
                } catch (_) {
                    if (navigator.mimeTypes["application/x-shockwave-flash"] != undefined) hasFlash = true
                }
                return hasFlash
            }
            if (!isFlashEnabled()) {
                document.getElementById("prechat").remove()
            } else {
                document.getElementById("flash-warn").remove()

            }
        </script>
    <?php
    }
    ?>

    <?php
    include_once("footer.php");
    ?>
</body>

</html>