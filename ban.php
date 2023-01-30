<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    session_start();
    include_once("head.php");
    include_once('backend/database.php');
    include_once('backend/usercontroller.php');

    $banned = false;
    $hashedIP = hash("sha512", getIp());
    $isBanned = $pdo->query("SELECT * FROM banip")->fetch();
    if (!empty($isBanned)) {

        foreach ($isBanned as $ip) {
            if ($ip === $hashedIP) {
                $banned = true;
                break;
            }
        }
    } else {
        header("location: /");
    }


    ?>
    <title>Blablaland - Compte banni</title>
</head>

<body>
    <?php include_once("header.php"); ?>

    <div class="jumbotron container">

        <div class="alert alert-danger">
            <center>
                <font size="4px"><i class="fa fa-info-circle"></i><strong> Vous avez été définitivement banni de Blablaland.ca !</strong><br><small><i>* Si vous pensez que c'est une erreur vous pouvez rejoindre le <a href="https://discord.gg/XHNEXSFkcC">Discord</a>.</i></small></font>
            </center>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br><br>
    <?php include_once("footer.php"); ?>

</body>

</html>