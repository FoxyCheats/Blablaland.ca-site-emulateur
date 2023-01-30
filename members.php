<?php
session_start();

include_once('backend/database.php');


$logged =  isset($_SESSION['api_token']);
$resMember = [];

if (isset($_GET['id'])) {
    $id = trim(htmlspecialchars($_GET['id']));
    $reqMember = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $reqMember->execute([$id]);
    $resMember = $reqMember->fetch();
}

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
    <?php include_once("head.php"); ?>
    <title>Blablaland - Membres</title>
    <style>
        #left img {
            height: 100px;
            width: 100px;
            border-radius: 10px;
        }

        #right {
            width: 70%;
        }

        .block {
            padding: 10px;
            display: block;
            box-shadow: 6px 4px 28px -4px rgba(0, 0, 0, 0.46);
            -webkit-box-shadow: 6px 4px 28px -4px rgba(0, 0, 0, 0.46);
            -moz-box-shadow: 6px 4px 28px -4px rgba(0, 0, 0, 0.46);
            box-shadow: 6px 4px 28px -4px rgba(0, 0, 0, 0.46);
            margin: 20px;
            width: 100%;
        }

        #right p {
            padding: 0;
            margin: 0;
        }

        .ibadge {
            height: 65px;
            width: 65px;
        }
    </style>
</head>

<body>
    <?php include_once("header.php"); ?>
    <div id="fond" class="container" style="width: 1000px;padding: 5px 40px 20px 40px;background: #fff;border-radius: 5px; -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);">
        <?php if (empty($resMember)) { ?>
            <center>
                <div class="alert alert-danger">Ce compte n'existe pas.</div>
            </center>
            <?php } else {
            if ($resMember["profile_theme"] == "1" && isset($_SESSION['theme']) && $_SESSION['theme']) { ?>
                <script>
                    var c = 'd';
                    var e = document.createElement('link');
                    e.id = c;
                    e.rel = 'stylesheet';
                    e.type = 'text/css';
                    e.href = 'frontend/css/bootstrap-dark.min.css';
                    e.media = 'all';
                    var f = document.getElementsByTagName('html')[0];
                    f.appendChild(e);
                    a = true;
                    var g = document.getElementById("switch");
                    g.innerHTML = "rallumer la lumière";
                    document.getElementById("prechat").style.backgroundColor = "#404040";
                    document.getElementsByClassName("dropdown-menu")[0].style.backgroundColor = "#252525";
                </script>
                <style>
                    #fond {
                        background: #252525 !important;
                        color: #dedede !important;
                    }

                    body {
                        color: #dedede !important;
                    }

                    .signa {
                        border: 1px solid #4c4c4c !important;
                    }
                </style>
            <?php } ?>

            <h3><i class="fa fa-user"></i> Profil de <?= $resMember['login']; ?><?php if ($logged && $resMember['api_token'] == $_SESSION['api_token']) { ?> - <a href="account.php">Modifier</a><?php } ?><u></u> </h3>
            <hr>



            <div id="left" style="float: left;">
                <div id="img" style="display: block; width: 130px; text-align: center;">
                    <div style="display: inline-block;width:130px;height:130px;margin: 10px;margin-left: 0px;background-size: 100% 100%;border-radius:10px;<?php if (!empty($resMember["avatar_image"])) {
                                                                                                                                                                echo "background-image: url({$resMember['avatar_image']});";
                                                                                                                                                            } else {
                                                                                                                                                                echo "background-image: url(frontend/img/avatar.png);";
                                                                                                                                                            } ?>background-color: #<?= $resMember["avatar_color"]; ?>">
                        <div id="viewskin2" style="background-color:#000000">
                        </div>
                    </div>

                    <script>
                        swfobject.embedSWF("swfs/viewskin.swf?CACHE_VERSION=467", "viewskin2", "100%", "100%", "8", null, {
                            ACTION: <?= $resMember["skinaction"]; ?>,
                            CACHE_VERSION: 467,
                            SKINID: "<?= $resMember["skinid"]; ?>",
                            SKINCOLOR: `<?php foreach (explode(',', $resMember["skincolors"]) as $color) {
                                            echo urlencode(chr($color + 1));
                                        } ?>`,
                            FONDID: "1",
                            SHOWSKIN: "<?= $resMember["show_skin"]; ?>",
                            USECACHE: "1",
                            HIDEBORDER: "1"
                        }, {
                            wmode: "transparent"
                        }, {
                            quality: "high",
                            scale: "noscale",
                            salign: "TL",
                            name: "viewskin2"
                        });
                    </script>

                    <?php isConnected($resMember); ?>

                    <br><span class="label label-danger" style="font-size: 12px"><?= $levels[$resMember['grade']]; ?></span><br>

                    <div id="badges">
                        <p>
                            <?php
                            foreach (explode(",", $resMember['badges']) as $badgeID) {
                                if ($badgeID !== "") {

                                    $badge = getBadge($pdo, $badgeID);
                            ?>
                                    <br>
                                    <img class="ibadge" title="<?= $badge['name']; ?>" src="<?= $badge['img'] ?>" style="height: 65px;width: 65px;"><br><small style="color: #FFBB00;"><b><?= $badge['name']; ?></b></small><br>
                            <?php
                                }
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>



            <div id="right" style="float: right; margin-right: 50px; ">


                <div class="block">
                    <h4>&nbsp;&nbsp;<i class="fa fa-info"></i>&nbsp;&nbsp;Informations</h4>
                    <hr>



                    <div id="content" style="margin: 20px;color:black;">
                        <span style="font-size: 16px"><i class="fa fa-user"></i> Pseudo en jeu : <?= $resMember['pseudo']; ?></span><br>
                        <?php
                        if ($resMember['genre'] == "1") {
                        ?><span style="font-size: 16px"><i class="fa fa-mars"></i> Genre : <?= "Garçon"; ?></span><br><?php
                                                                                                                    } else if ($resMember['genre'] == "2") {
                                                                                                                        ?><span style="font-size: 16px"><i class="fa fa-venus"></i> Genre : <?= "Fille"; ?></span><br><?php
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        echo '<span style="font-size: 16px"><i class="fa fa-transgender"></i> Genre : Non spécifié</span><br>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                        ?>
                        <span style="font-size: 16px"><i class="fa fa-star"></i> XP's : <?= $resMember['xp'] . " XP's"; ?></span>
                    </div>




                    <div id="content1" style="margin: 20px;color: black;">
                        <span style="font-size: 16px"><i class="fa fa-sign-in"></i> Inscrit(e) le : <?= date("d/m/Y à H:i:s", $resMember["registerdate"]); ?></span><br>

                        <span style="font-size: 16px"><i class="fa fa-flag"></i> Pays : <?= $countries[$resMember["pays"]]; ?></span><br>
                    </div>

                </div>
                <!-- Vérifier quelemembre a cocher skin_view -->
                <?php
                if ($resMember['show_skin'] === true) { ?>

                    <div class="block">
                        <h4>&nbsp;&nbsp;<i class="fa fa-info"></i>&nbsp;&nbsp;Skin</h4>
                        <hr>
                        <div id="viewskin2"></div>
                        <script>
                            swfobject.embedSWF("swfs/viewskin.swf?CACHE_VERSION=467", "viewskin2", "100%", "100%", "8", null, {
                                ACTION: <?= $resMember["skinaction"]; ?>,
                                CACHE_VERSION: 467,
                                SKINID: "<?= $resMember["skinid"]; ?>",
                                SKINCOLOR: `<?php foreach (explode(',', $resMember["skincolors"]) as $color) {
                                                echo urlencode(chr($color + 1));
                                            } ?>`,
                                FONDID: "1",
                                SHOWSKIN: "<?= $resMember["show_skin"]; ?>",
                                USECACHE: "1",
                                HIDEBORDER: "1"
                            }, {
                                wmode: "transparent"
                            }, {
                                quality: "high",
                                scale: "noscale",
                                salign: "TL",
                                name: "viewskin2"
                            });
                        </script>


                    </div>
                <?php

                } ?>


                <div class="block">
                    <h4><i class="fa fa-thumb-tack"></i> Signature</h4>
                    <hr>
                    <?php
                    if (empty($resMember['signature'])) {
                        echo "Aucune signature définie par le joueur.";
                    } else {

                        echo $resMember['signature'];
                    }
                    ?>
                </div>
            </div>


        <?php } ?>

    </div>
    <?php
    include_once('footer.php');
    ?>
</body>

</html>
