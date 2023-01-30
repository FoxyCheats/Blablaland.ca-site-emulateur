<?php
session_start();

include_once('backend/database.php');
include_once('backend/usercontroller.php');
$reqNews = $pdo->query("SELECT * FROM news");
$resUsersTotal = $pdo->query("SELECT * FROM users")->rowCount();
$resUsersOnline = $pdo->query("SELECT * FROM users WHERE online_chat = 1")->rowCount();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <?php
    include_once("head.php");
    ?>
    <title>Blablaland - Accueil</title>
</head>

<body>
    <?php
    include_once("header.php");
    ?>

    <div class="container bg-white">

        <div class="jumbotron" style="background-color: white;">
            <div style="float: right;">
                <iframe style="margin-top: 125px;" src="https://discord.com/widget?id=1056160709425238026&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
                <br>

            </div>
            <h1>Bienvenue à toi<?php if ($logged) {
                                    $account = getAccount($pdo, $_SESSION['api_token']);
                                    echo " "  . $account['login'];
                                } ?> !</h1>
                                <br>
            <p style="font-size: 22px;">
                Blablaland.ca est un serveur privé du jeu Blablaland,<br>cela signifie que tu y retrouveras le même univers
                mais<br>sans limites, tous les skins et les pouvoirs sont à ta<br>disposition et gratuitement
                ^^.<br>Prêt
                pour un nouveau départ ?
            </p>
            <br><br>
            <span class="w-100 pr-0 mb-1" style="display:inline-block;"><small>
                    <div class="row">
                        <img src="imgs/8.jpg" width="40" height="40" style="float: left; margin-left: 25px; margin-right: 10px; margin-top: 15px;" class="col-lg-auto">
                        <h4 style="float: right;" class="col-lg-auto">
                            Nous sommes actuellement <b style="color:#ad0051"><?= $resUsersTotal; ?></b> inscrits !
                            <br>
                            Rejoins les <b style="color:green"><?= $resUsersOnline; ?></b> connectés sur le T'chat !
                        </h4>
                    </div>
                </small></span>
        </div>


        <div class="jumbotron" style="background-color:white">
            <h1><i class="fa fa-angellist"></i> Nouveautés</h1>
            <?php
            if ($reqNews->rowCount() > 0) {

                while ($resNews = $reqNews->fetch()) {
            ?>
                    <h5><u><a class="text-information" href="news.php?id=<?= $resNews['id']; ?>"><?= $resNews['titre']; ?></a></u></h5>
                    <hr>

                <?php
                }
            } else {
                ?>
                <h5>
                    <p class="text-information">Rien de neuf à signaler.</p>
                </h5>

            <?php
            }
            ?>
        </div>

    </div>
    </div>
    <?php include_once("footer.php"); ?>
</body>

</html>
