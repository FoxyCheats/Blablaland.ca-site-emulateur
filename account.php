<?php
session_start();
include_once('backend/database.php');
include_once('backend/api.php');
include_once('backend/usercontroller.php');

if (!isset($_SESSION['api_token'])) {
    return header('location: /signin.php');
}
$result = [];
$account = getAccount($pdo, $_SESSION['api_token']);

if (isset($_POST['username'])) {
    $username = trim(htmlspecialchars($_POST['username']));
    if (!empty($username)) {
        if (strlen($username) <= 25) {
            $result = usernameAlreadyTaken($pdo, $username);
            if ($result === false) {
                updateAccount($pdo, $account['pseudo'], $username, "pseudo", $account["api_token"]);
            } else {
                $result = ['username_already_taken' => "Le nom d'utilisateur est déjà utilisé !"];
            }
        } else {
            $result = ['username_too_long' => "Le nom d'utilisateur ne doit pas dépasser 25 caractères !"];
        }
    }
}

if (isset($_POST['email'])) {
    $email = trim(htmlspecialchars($_POST['email']));
    if (!empty($email)) {
        if ($account['email'] !== $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result = emailAlreadyTaken($pdo, $email);
                if ($result === false) {
                    updateAccount($pdo, $account['email'], cipherEmail($email), "email", $account["api_token"]);
                    $apiToken = genAPIToken($account['login'], $account['password'], $email);
                    updateAccount($pdo, $account['api_token'], $apiToken, "api_token", $account["api_token"]);
                    $account['api_token'] = $apiToken;
                    $_SESSION['api_token'] = $apiToken;
                    emailVerification($email, $apiToken);
                } else {
                    $result = ['email_already_taken' => "L'adresse mail est déjà utilisée !"];
                }
            } else {
                $result = ['invalid_email' => "L'adresse mail est invalide !"];
            }
        }
    }
}

if (isset($_POST['country'])) {
    $country = trim(htmlspecialchars($_POST['country']));
    if (!empty($country)) {
        updateAccount($pdo, $account['pays'], $country, "pays", $account["api_token"]);
    }
}

if (isset($_POST['gender'])) {
    $gender = trim(htmlspecialchars($_POST['gender']));
    updateAccount($pdo, $account['genre'], $gender, "genre", $account["api_token"]);
}

if (isset($_POST['clan'])) {
    $clan = trim(htmlspecialchars($_POST['clan']));
    if (!empty($clan)) {
        if (strlen($clan) <= 4) {

            updateAccount($pdo, $account['clan'], $clan, "clan", $account["api_token"]);
        } else {
            $result['clan_name_too_long'] = "Le nom du clan ne doit pas dépasser 4 caractères !";
        }
    } else {
        updateAccount($pdo, $account['clan'], "", "clan", $account["api_token"]);
    }
}

if (isset($_POST['edit_avatar'])) {
    if (empty($_POST['show_skin'])) {
        updateAccount($pdo, $account['show_skin'], 0, "show_skin", $account["api_token"]);
    } else {
        updateAccount($pdo, $account['show_skin'], 1, "show_skin", $account["api_token"]);
    }
}

if (isset($_POST['avatar_color'])) {
    $avatar_color = $_POST['avatar_color'];
    updateAccount($pdo, $account['avatar_color'], str_replace("#", "", $avatar_color), "avatar_color", $account["api_token"]);
}

if (isset($_POST['signature'])) {
    $signature = trim(htmlspecialchars($_POST['signature']));
    if (!empty($signature)) {
        updateAccount($pdo, $account['signature'], $signature, "signature", $account["api_token"]);
    }
}

if (isset($_FILES['avatar_image'])) {
    if (!empty($_FILES['avatar_image'])) {
        $avatar_image = $_FILES['avatar_image'];
        if (!empty($avatar_image['tmp_name'])) {
            $imageExt = strtolower(pathinfo(basename($avatar_image["name"]), PATHINFO_EXTENSION));
            $isImg = explode("/", mime_content_type($avatar_image['tmp_name']))[0];
            if (explode("/", mime_content_type($avatar_image['tmp_name']))[0] === "image") {
                if ($avatar_image['size'] <= 2000000) {
                    $baseURL = "upload/avatars/" . urlencode($account['ID'] . time() . hash("md5", basename($avatar_image["name"])) . "." . $imageExt);
                    copy($avatar_image['tmp_name'], $baseURL);
                    updateAccount($pdo, $account['avatar_image'], $baseURL, "avatar_image", $account["api_token"]);
                } else {
                    $result['too_big_image'] = "La taille de l'image ne doit pas dépasser 40 MB !";
                }
            } else {
                $result['invalid_image'] = "Le fichier téléchargé n'est pas une image !";
            }
        }
    }
}

if (isset($_POST['last-password']) && isset($_POST['new-password']) && isset($_POST['conf-new-password'])) {
    $lastPassword = $_POST['last-password'];
    if (password_verify($lastPassword, $account['password'])) {
        $newPassword = $_POST['new-password'];
        $confNewPassword = $_POST['conf-new-password'];
        if ($newPassword === $confNewPassword) {
            $lastPassword = password_hash($lastPassword, PASSWORD_BCRYPT);
            $newPassword = $_POST['new-password'];
            $confNewPassword = password_hash($_POST['conf-new-password'], PASSWORD_BCRYPT);
            if (!password_verify($newPassword, $account['password'])) {
                $newPassword = password_hash($_POST['new-password'], PASSWORD_BCRYPT);

                updateAccount($pdo, $account['password'], $newPassword, "password", $account["api_token"]);
                $apiToken = genAPIToken($account['username'], $newPassword, $account['email']);
                updateAccount($pdo, $account['api_token'], $apiToken, "api_token", $account["api_token"]);
                return header("location: /signin.php");
            } else {
                $result['password_equals_last'] = "Le nouveau mot de passe ne doit pas être le même que celui actuel !";
            }
        } else {
            $result['invalid_password_confirmation'] = "Le nouveau mot de passe ne correspond pas à sa confirmation !";
        }
    } else {
        $result['invalid_password'] = "Le mot de passe actuel ne correspond pas à celui entré !";
    }
}

if (isset($_POST['birthdate'])) {
    $birthDate = strtotime(trim(htmlspecialchars($_POST['birthdate'])));
    if (!empty($birthDate)) {
        updateAccount($pdo, null, $birthDate, "birthdate", $account['api_token']);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    include_once("head.php");
    ?>
    <title>Blablaland - Compte</title>
</head>

<body>
    <?php
    include_once("header.php");
    ?>



    <div class="container" id="fond" style="width: 1000px;padding: 5px 40px 20px 40px;background: #fff;border-radius: 5px; -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.3);">
        <?php
        if (!empty($result)) { ?>
            <div class="errors bg-danger alert">
                <ul>
                    <?php foreach ($result as $error) { ?>
                        <li><?= $error ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <h3><i class="fa fa-cog"></i> Mon compte
            <?php if ($account["grade"] == 1000) { ?>
                <div style="float:right;font-size: 25px;color:red;"><b><span id="rainbow">Créateur de Blablaland</span></b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></div>
            <?php } else if ($account["grade"] == 50) { ?>
                <span style="float:right;font-size: 25px;color:orange;"><b>Ancien membre du staff</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } else if ($account["grade"] == 1001) { ?>
                <span style="float:right;font-size: 25px;color:orange;"><b>Créatrice de Blablaland</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } else if ($account["grade"] == 801) { ?>
                <span style="float:right;font-size: 25px;color:orange;"><b>Responsable du staff</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } else if ($account["grade"] == 700) { ?>
                <span style="float:right;font-size: 25px;color:orange;"><b>Graphiste</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } else if ($account["grade"] == 500) { ?>
                <span style="float:right;font-size: 25px;color:blue;"><b>Modérateur</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } else if ($account["grade"] == 200) { ?>
                <span style="float:right;font-size: 25px;color:blue;"><b>Animateur</b> <img style="width: 45px;" src="frontend/img/8.jpg"></img></span>
            <?php } ?>
        </h3>
        <hr>

        <?php if ($account["profile_theme"] == "1" && isset($_SESSION['theme']) && $_SESSION['theme']) { ?>
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
        <h3 style="display:inline;color:#F50085">
            <b><?= $account['login']; ?></b></h3>
        <br>
        <br>Pseudo en tchat : <?= $account['pseudo']; ?> &nbsp;&nbsp;&nbsp;<a href="#username"><small style="display:inline;padding:0;">Modifier</small></a>
        <br>
        <div style="width: 250px;display: inline-block;">
            <div style="vertical-align: top;display: inline-block;margin-right: 10px;width:130px;height:130px;margin: 10px;margin-left: 0px;background-size: 100% 100%;border-radius:10px;background-color:<?= "#" . $account['avatar_color']; ?> !important;background-image:<?= "url(" . (strlen($account['avatar_image']) > 0 ? $account['avatar_image'] : "frontend/img/avatar.png") . ")"; ?>;"></div>

            <div style="display:inline-block;">
                <br><?php if ($account['genre'] == "0") { ?><i class="fa fa-transgender"></i> Non spécifié<?php } else if ($account['genre'] == "1") { ?><font color="blue"><i class="fa fa-mars"></i> Garçon</font><?php } else if ($account['genre'] == "2") { ?><font style="color:#ff00c6"><i class="fa fa-venus"></i> Fille</font><?php } ?>
                <br><b><span id="BBLINFOS_XP" name="BBLINFOS_XP" class="label label-primary"><?= number_format($account["xp"], 0, '.', ' '); ?> XP</span></b>
                <br><b><?php

                        foreach ($levels as $xp => $titre) {
                            if ($account["xp"] < $xp) {
                                $level = $lastNiv;
                                break;
                            }
                            $lastNiv = $titre;
                            $level = "Blabla Suprême";
                        }

                        echo $level;

                        ?></b>
                <br>
                <?php if ($account["online_chat"]) { ?>
                    <font color="green"><i class="fa fa-circle"></i> <b> Chat</b></font>
                <?php } else { ?>
                    <font color="red"><i class="fa fa-circle"></i> Chat</font>
                <?php } ?>
            </div>
        </div>
        <div style="display:inline;">
            <img style="position: absolute; margin-top: 20px;" src="frontend/img/talk.gif"></img>
            <div class="signa" style="-webkit-box-shadow: 4px 4px 4px 0px rgba(0,0,0,0.15);-moz-box-shadow: 4px 4px 4px 0px rgba(0,0,0,0.15);box-shadow: 4px 4px 4px 0px rgba(0,0,0,0.15);float:right;border: 1px solid #cecece;width:651px;padding:20px;padding-top:0;margin-bottom:30px;border-radius:5px;">
                <?php if (empty($account["signature"])) {
                    echo "<i>Aucune signature...</i>";
                } else {
                    echo "<br>" . $account["signature"];
                }
                ?> <a href="#signature"><small style="display:inline;font-size: 12px;">Modifier</small></a>
            </div>
        </div>
        <script>
            swfobject.embedSWF("swfs/viewskin.swf?CACHE_VERSION=467", "viewskin",
                "54",
                "54",
                "20",
                "swfs/expressInstall.swf", {
                    ACTION: <?= $account["skinaction"]; ?>,
                    CACHE_VERSION: 467,
                    SKINID: "<?= $account["skinid"]; ?>",
                    SKINCOLOR: `<?php foreach (explode(',', $account["skincolors"]) as $color) {
                                    echo urlencode(chr($color + 1));
                                } ?>`,
                    FONDID: "1",
                    SHOWSKIN: "<?= $account["show_skin"] == true ? "1" : "0"; ?>",
                    USECACHE: "1",
                    HIDEBORDER: "1"
                }, {
                    wmode: "transparent"
                }, {
                    quality: "high",
                    scale: "noscale",
                    salign: "TL",
                    name: "viewskin"
                });
        </script>
        <br>
        <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Inscrit(e) le : <i><?= date("d/m/Y à H:i:s", $account["registerdate"]); ?></i><br>
        <i class="fa fa-birthday-cake"></i>&nbsp;&nbsp;Date d'anniversaire : <i><?php if (isset($account["birthdate"]) && !empty($account["birthdate"])) {
                                                                                    echo date("d/m/Y", $account["birthdate"]);
                                                                                } else {
                                                                                    echo "Non spécifié";
                                                                                } ?></i><br>
        <i class="fa fa-users"></i>&nbsp;&nbsp;Clan : <i><?php if (strlen($account["clan"]) > 0) {
                                                                echo $account["clan"];
                                                            } else {
                                                                echo "Aucun";
                                                            }; ?></i><br>
        <i class="fa fa-globe"></i>&nbsp;&nbsp;Pays : <?php if (empty($account["pays"])) { ?><i>Aucun</i><?php } else { ?><img style="margin: 0 5px 2px 5px;width:24px;-webkit-box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.5);-moz-box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.5);box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.5);" src="frontend/img/flags/4x3/<?= strtolower($account["pays"]); ?>.svg">&nbsp;<?= $countries[$account["pays"]]; ?><?php } ?><br>

            <?php if (isset($accountOwner)) { ?> <a href="#infos"><small>Modifier</small></a><?php } ?>
            <br>
            <div style="clear: both">
            </div>
            <br>

            <hr>

            <h4 id="username"><i class="fa fa-pencil"></i> Changer de pseudo</h4>
            <hr>
            <center>
                <form method="POST" style="width: 300px; margin: 0 auto;">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        <input maxlength="25" class="form-control" type="text" name="username" placeholder="username" value="<?= $account['pseudo']; ?>"><small>Maximum 25 charactères. (Essayez de le différencier de votre mot de passe)</small>
                    </div><br>
                    <button type="submit" name="edit_pseudo" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center><br>

            <h4 id="infos"><i class="fa fa-pencil"></i> Changer mes infos</h4>
            <hr>
            <center>
                <form method="POST" style="width: 400px; margin: 0 auto;">

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        <input class="form-control" type="email" name="email" placeholder="email" value="<?= $account["email"]; ?>"><small>Email de réinitialisation de mot de passe et confirmation d'IPs et de compte. (Essayez de la différencier de votre mot de passe)</small>
                    </div><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-birthday-cake"></i>
                        </span>
                        <input class="form-control" type="date" name="birthdate" value="<?php if (isset($account["birthdate"]) && !empty($account["birthdate"])) {
                                                                                            echo date("Y-m-d", $account["birthdate"]);
                                                                                        } ?>"><small>Indiquez votre date d'anniversaire.</small>
                    </div><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-globe"></i>
                        </span>
                        <select name="country" class="form-control">
                            <?php
                            foreach ($countries as $key => $value) {
                                $add = "";
                                if (isset($account["pays"]) && $account["pays"] == $key) $add = "selected";
                                echo "<option value=\"$key\" $add>$value</option>";
                            }
                            ?>
                        </select>
                    </div><br>
                    <div style="margin: 0 auto;" class="input-group input-group-lg">
                        Genre :
                        <input type="radio" name="gender" value="1" <?php if ($account['genre'] == "1") echo "checked"; ?>>
                        Un garçon &nbsp;&nbsp;
                        <input type="radio" name="gender" value="2" <?php if ($account['genre'] == "2") echo "checked"; ?>>
                        Une fille&nbsp;&nbsp;
                        <input type="radio" name="gender" value="0" <?php if ($account['genre'] == "0") echo "checked"; ?>>
                        Non spécifié &nbsp;&nbsp;
                    </div><br>
                    <button type="submit" name="edit_infos" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center><br>

            <h4 id="infos"><i class="fa fa-pencil"></i> Changer de mot de passe</h4>

            <center>
                <form method="POST" style="width: 400px; margin: 0 auto;">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-unlock"></i>
                        </span>
                        <input class="form-control" type="password" name="last-password" placeholder="Ancien mot de passe">
                    </div><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-unlock"></i>
                        </span>
                        <input class="form-control" type="password" name="new-password" placeholder="Nouveau mot de passe">
                        <small>Évitez un lien entre votre adresse mail/nom d'utilisateur et votre nouveau mot de passe.</small>
                    </div><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-unlock"></i>
                        </span>
                        <input class="form-control" type="password" name="conf-new-password" placeholder="Confirmation du nouveau mot de passe">
                        <small>Doit correspondre au champs précédent.</small>
                    </div><br>

                    <button type="submit" name="edit_password" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center><br>

            <h4><i class="fa fa-pencil"></i> Changer de clan</h4>
            <hr>
            <center>
                <form method="POST" style="width: 400px; margin: 0 auto;">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-users"></i>
                        </span>
                        <input class="form-control" type="text" name="clan" maxlength="4" placeholder="Clan" value="<?php if (isset($account["clan"])) echo $account["clan"]; ?>"><small>Maximum 4 caractères. Laissez vide pour pas de clan, peut aussi être changé en jeu avec la commande !clan.</small>
                    </div>
                    <br>
                    <button type="submit" name="edit_clan" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center><br>

            <h4><i class="fa fa-pencil"></i> Changer de skin</h4>
            <hr>
            <center>
                <object type="application/x-shockwave-flash" data="swfs/profil.swf" height="550" width="600">
                    <param name="bgcolor" value="#222">
                    <param name="wmode" value="transparent">
                    <param name="flashvars" value="SESSION=<?= $account["session"]; ?>&amp;USERID=<?= $_SESSION["ID"]; ?>">
                </object>
            </center><br>
            <div id="profilSkin" style="background-color:#000000;display: inline-block;"></div>

            <h4><i class="fa fa-pencil"></i> Changer d'avatar</h4>
            <hr>
            <center>
                <form method="POST" style="width: 400px; margin: 0 auto;" enctype="multipart/form-data">
                    <input type="checkbox" id="show_skin" name="show_skin" value="show_skin" <?php if (isset($account["show_skin"]) && $account["show_skin"] == 1) {
                                                                                                    echo "checked";
                                                                                                } ?>>
                    <label for="show_skin">Afficher mon skin</label><br><br>

                    <input type="color" id="avatar_color" name="avatar_color" value="#<?= htmlspecialchars($account["avatar_color"]); ?>">
                    <label for="avatar_color">Couleur de fond</label><br><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-picture-o"></i>
                        </span>
                        <input class="form-control" type="file" name="avatar_image" accept="image/*">
                    </div><br>
                    <button type="submit" name="edit_avatar" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center><br>

            <h4 id="signature"><i class="fa fa-pencil"></i> Modifier ma signature</h4>
            <hr>
            <center>
                <form method="POST" style="margin: 0 auto; width: 400px;">
                    <textarea id="editor" name="signature" style="resize: none;" placeholder="Change ta signature"><?php if (isset($account["signature"])) echo $account["signature"]; ?></textarea>
                    <br><br>
                    <button type="submit" name="edit_signature" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>
                </form>
            </center>

            <?php


            $reqBadge = $pdo->prepare("SELECT users.pseudo, users, users.ID, badges.img, badges.name FROM badges, user_badges, users WHERE badges.id = user_badges.badge_id AND users.id = user_badges.user_id AND users.id = ?");
            $reqBadge->execute([$account['ID']]);
            if ($reqBadge->rowCount() > 0) {
            ?><br>

                <h4 id="signature"><i class="fa fa-pencil"></i> Modifier mes badges</h4>
                <style type="text/css">
                    .ibadge {
                        height: 40px;
                        width: 40px;
                    }
                </style>
                <hr>
                <center>
                    <ul><?php
                    }
                    foreach ($reqBadge as $resBadgeUser) {
                        ?>
                        <img class="ibadge" title="<?= $resBadgeUser[0]; ?>" src="<?= $resBadgeUser[3]; ?>"><a href="delete_badge.php?id=<?= $resBadgeUser[2]; ?>&badge_id=<?= $resBadgeUser[1]; ?>">Supprimer</a>
                    <?php
                    }

                    ?>
                    </ul>
                </center><br>

    </div>
    <?php include_once("footer.php"); ?>
</body>

</html>
