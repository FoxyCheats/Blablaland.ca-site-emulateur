<?php
session_start();

include_once('backend/database.php');
include_once('backend/usercontroller.php');

$reqAnimAndSuper = $pdo->query("SELECT * FROM users WHERE grade = 200 OR grade = 600");
$reqModoAndSuper = $pdo->query("SELECT * FROM users WHERE grade = 500 OR grade = 801");
$reqGraph = $pdo->query("SELECT * FROM users WHERE grade = 700");
$reqDev = $pdo->query("SELECT * FROM users WHERE grade = 1000 OR grade = 1001");
$reqVet = $pdo->query("SELECT * FROM users WHERE grade = 50");
$reqOnlineStaff = $pdo->prepare("SELECT * FROM users WHERE online_chat = 1 AND grade >= 200");
$reqOnlineStaff->execute();
$onlineStaffLength = $reqOnlineStaff->rowCount();
function isConnected($user)
{
    if ($user['online_chat'] == 1) {
?>
        <font color="green"><i class="fa fa-circle"></i> <b>Chat</b></font>
    <?php
    } else {
    ?>
        <font color="red"><i class="fa fa-circle"></i> <b>Chat</b></font>
<?php
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    include_once("head.php");
    ?>
    <title>Blablaland - Équipe</title>
</head>

<body>
    <?php
    include_once("header.php");
    ?>

    <div class="container">
        <h3><i class="fa fa-users"></i> Équipe </h3>
        <font style="color: green; font-size: 28px;"><i class="fa fa-circle"></i><b> <?= $onlineStaffLength ?></b> connectés</font>
        <hr>

        <h4>L'équipe de Blablaland.ca</h4>
        <hr>
        <div class="col-sm-12">

            <?php while ($req = $reqDev->fetch()) { ?>
                <div class="col-sm-2 text-center">
                    <a href="/members.php?id=<?= $req['ID'] ?>" style="font-size:1.7rem;color:red;font-weight:bold"><?= $req['login'] ?></a>
                    <img style="width:130px;height:130px;border-radius:10px" src="<?= ($req['avatar_image']) ? $req['avatar_image'] : 'frontend/img/avatar.png'; ?>" /><br>

                    <p class="grade"><?= $levels[$req['grade']]; ?></p>
                    <p><?php isConnected($req); ?></p>
                </div>
            <?php } ?>
        </div>

        <h4>Graphiste</h4>
        <hr>
        <div class="col-sm-12">
            <?php while ($req = $reqGraph->fetch()) { ?>
                <div class="col-sm-2 text-center">
                    <a href="/members.php?id=<?= $req['ID'] ?>" style="font-size:1.7rem;color:#0000FF;font-weight:bold"><?= $req['login'] ?></a>
                    <img style="width:130px;height:130px;border-radius:10px" src="<?= ($req['avatar_image']) ? $req['avatar_image'] : 'frontend/img/avatar.png'; ?>" /><br>

                    <p class="grade"><?= $req['staff_titre']; ?></p>
                    <p><?php isConnected($req); ?></p>
                </div>
            <?php } ?>
        </div>


        <h4>Modération</h4>
        <hr>
        <div class="col-sm-12">
            <?php while ($req = $reqModoAndSuper->fetch()) { ?>
                <div class="col-sm-2 text-center">
                    <a href="/members.php?id=<?= $req['ID'] ?>" style="font-size:1.7rem;color:#0000FF;font-weight:bold"><?= $req['login'] ?></a>
                    <img style="width:130px;height:130px;border-radius:10px" src="<?= ($req['avatar_image']) ? $req['avatar_image'] : 'frontend/img/avatar.png'; ?>" /><br>

                    <p class="grade"><?= $req['staff_titre']; ?></p>
                    <p><?php isConnected($req); ?></p>
                </div>
            <?php } ?>
        </div>



        <h4>Animation</h4>
        <hr>
        <div class="col-sm-12">
            <?php while ($req = $reqAnimAndSuper->fetch()) { ?>
                <div class="col-sm-2 text-center">
                    <a href="/members.php?id=<?= $req['ID'] ?>" style="font-size:1.7rem;color:#00A000;font-weight:bold"><?= $req['login'] ?></a>
                    <img style="width:130px;height:130px;border-radius:10px" src="<?= ($req['avatar_image']) ? $req['avatar_image'] : 'frontend/img/avatar.png'; ?>" /><br>

                    <p class="grade"><?= $req['staff_titre']; ?></p>
                    <p><?php isConnected($req); ?></p>
                </div>
            <?php } ?>
        </div>


        <h4>Ancien Staff</h4>
        <hr>
        <div class="col-sm-12">
            <?php while ($req = $reqVet->fetch()) { ?>
                <div class="col-sm-2 text-center">
                    <a href="/members.php?id=<?= $req['ID'] ?>" style="font-size:1.7rem;color:#0000FF;font-weight:bold"><?= $req['login'] ?></a>
                    <img style="width:130px;height:130px;border-radius:10px" src="<?= ($req['avatar_image']) ? $req['avatar_image'] : 'frontend/img/avatar.png'; ?>" /><br>

                    <p class="grade"><?= $req['staff_titre']; ?></p>
                    <p><?php isConnected($req); ?></p>
                </div>
            <?php } ?>
        </div>





    </div>

    <?php
    include_once("footer.php");
    ?>
</body>

</html>