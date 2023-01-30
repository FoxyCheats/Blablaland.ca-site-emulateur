<?php
session_start();

include_once('backend/database.php');
include_once('backend/usercontroller.php');
include_once("shop_navbars.php");
include_once('backend/api.php');

$tableSkinIndex = 0;

if (empty($_SESSION['api_token'])) {
    return header("location: /signin.php");
}

$account = getAccount($pdo, $_SESSION['api_token']);
$result = [];

if (isset($_POST['buy']) && isset($_POST['badge-id'])) {
    if ($_POST['badge-id'] === "0" || !empty($_POST['badge-id'])) {
        $badgeID = $_POST['badge-id'];
        $badge = getBadge($pdo, $badgeID);
        if (!empty($badge)) {

            if (!in_array($badgeID, explode(",", $account['badges']))) {
                if ($account['bbl'] - $badge['prix'] >= 0) {
                    $query = $account['badges'] . $badgeID . ",";
                    updateAccount($pdo, 0, $query, "badges", $account['api_token']);
                    updateAccount($pdo, 0, $account['bbl'] - $badge['prix'], "bbl", $account['api_token']);
                } else {
                    $result['not_enough_bbl'] = "Il semblerait que vous n'ayez pas suffisamment de BLL pour acheter ce badge !";
                }
            } else {
                $result['invalid_badgeid'] = "Le badge ne semble pas disponible, rafraichissez la page puis réessayez !";
            }
        }
    } else {
        $result['invalid_badgeid'] = "Le badge ne semble pas disponible, rafraichissez la page puis réessayez !";
    }
}

if (isset($_POST['buy']) && isset($_POST['skin-id'])) {
    if ($_POST['skin-id'] === "0" || !empty($_POST['skin-id'])) {
        $skinID = $_POST['skin-id'];
        $skin = getSkin($pdo, $skinID);
        if (!empty($skin)) {
            if (!in_array($skinID, explode(",", $account['skinsList']))) {
                if ($account['bbl'] - $skin['montant'] >= 0) {
                    $query = $account['skinsList'] . $skinID . ",";
                    updateAccount($pdo, 0, $query, "skinsList", $account['api_token']);
                    updateAccount($pdo, 0, $account['bbl'] - $skin['montant'], "bbl", $account['api_token']);
                } else {
                    $result['not_enough_bbl'] = "Il semblerait que vous n'ayez pas suffisamment de BLL pour acheter ce skin !";
                }
            }
        } else {
            $result['not_disponible_skin'] = "Il semblerait que le skin que vous tentez d'acheter ne soit plus disponible !";
        }
    } else {
        $result['invalid_skinid'] = "Le skin ne semble pas disponible, rafraichissez la page puis réessayez !";
    }
}

if (isset($_GET) && !empty($_GET['id_skin'])) {
    try {
        $tableSkinIndex = intval(trim(htmlspecialchars($_GET['id_skin'])));
    } catch (\Exception $e) {
        $tableSkinIndex = 0;
    }
    if ($tableSkinIndex < 0) {
        $tableSkinIndex = 0;
    }
} else {
    $tableSkinIndex = 0;
}

$range = $tableSkinIndex * 10;
$skins = $pdo->query("SELECT * FROM skins WHERE disponible = 1 LIMIT 10 OFFSET $range")->fetchAll();

if (isset($_POST['skin_query'])) {
    if (isset($_POST['skin_name'])) {
        $skinName = trim(htmlspecialchars($_POST['skin_name']));
        if (!empty($skinName)) {
            $skins = [];
            $skinsReq = $pdo->query("SELECT * FROM skins WHERE disponible = 1");
            if (!empty($skinsReq)) {
                $skinsRes = $skinsReq->fetchAll();
                foreach ($skinsRes as $skin) {
                    if (in_array(strtolower($skinName), explode(' ', strtolower($skin['name']))) || in_array(strtolower($skinName), explode(' ', strtolower($skin['comment'])))) {
                        array_push($skins, $skin);
                    }
                }
            } else {
                $result['unfound_skin'] = "Le skin que vous cherchez n'a pas été trouvé !";
            }
        } else {
            $result['empty_skinname'] = "Vous devez indiquer un nom de skin !";
        }
    } else {
        $result['empty_skinname'] = "Vous devez indiquer un nom de skin !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Boutique</title>
</head>

<body>
    <?php include_once("header.php");
    if (!empty($result)) { ?>
        <div class="errors bg-danger alert">
            <ul>
                <?php foreach ($result as $error) {
                ?>
                    <li><?= $error ?></li>
                <?php
                } ?>
            </ul>
        </div>
    <?php }
    ?>
    <div class="container jumbotron">
        <h3 style="text-align: center;">Boutique de badges</h3>
        <?php
        if (!empty($badges)) {
        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="col-sm-2">Preview de badge</th>
                        <th scope="col" class="col-sm-2">Nom du badge</th>
                        <th scope="col" class="col-sm-6">Description</th>
                        <th scope="col" class="col-sm-2">Prix</th>
                        <th scope="col" class="col-sm-2">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($badges as $badge) {
                        if (empty($account['badges']) || !in_array($badge['id'], explode(",", $account['badges']))) {
                            echo "<tr><td><img width=\"150\" height=\"150\" src=\"{$badge['img']}\"></td>
                            <td>{$badge['name']}</td>
                            <td>{$badge['description']}</td>
                            <td>{$badge['prix']}</td>
                            <td><form method=\"POST\">
                                <button class=\"btn btn-success\" type=\"submit\" name=\"buy\">Acheter</button>
                                <input type=\"hidden\" name=\"badge-id\" value=\"{$badge['id']}\">
                        </form></td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php
        } else {
            echo "<center><p>Aucun badge n'est disponible ici.</p></center>";
        }
        ?>
    </div>

    <div class="container jumbotron">
        <h3 style="text-align: center;">Boutique de skins</h3>
        <br>
        <div class="query row">
            <?php
            if (1 === 2) {
            ?>
                <div class="col-lg-6 category">
                    <center>
                        <form method="POST">
                            <select name="category">
                                <option value="0" <?php if (isset($_POST['category']) && $_POST['category'] == "0") {
                                                        echo "selected";
                                                    } ?>>Normal</option>
                                <option value="1" <?php if (isset($_POST['category']) && $_POST['category'] == "1") {
                                                        echo "selected";
                                                    } ?>>Exclusif</option>
                                <option value="2" <?php if (isset($_POST['category']) && $_POST['category'] == "2") {
                                                        echo "selected";
                                                    } ?>>Modérateur</option>
                                <option value="3" <?php if (isset($_POST['category']) && $_POST['category'] == "3") {
                                                        echo "selected";
                                                    } ?>>Fondateur</option>
                            </select>
                            <input type="submit" class="btn btn-primary" style="margin-left: 25px;" name="skin_category" value="Chercher">
                        </form>
                    </center>
                </div>

            <?php
            } ?>
            <div class="col-lg-6 search">
                <center>
                    <form method="POST" class="row">
                        <input type="text" class="input-form" style="width: 50%; text-align:center;" name="skin_name" placeholder="Entrez le nom du skin">
                        <input type="submit" class="btn btn-primary" style="margin-left: 25px;" name="skin_query" value="Rechercher">
                    </form>
                </center>
            </div>

        </div>
        <br><br>

        <?php
        if (!empty($skins)) {
        ?>
            <center>
                <?php skinBeforeAndNext($tableSkinIndex); ?>
            </center>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="col-sm-2">Preview</th>
                        <th scope="col" class="col-sm-2">Nom du skin</th>
                        <th scope="col" class="col-sm-4">Description</th>
                        <th scope="col" class="col-sm-2">Prix</th>
                        <th scope="col" class="col-sm-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 0;
                    $resMember = getAccount($pdo, $_SESSION['api_token']);
                    foreach ($skins as $skin) {
                        if (intval($skin['grade']) <= intval($account['grade']) && !in_array($skin['id'], explode(",", $resMember['skinsList']))) {

                            $payload = "";
                            foreach (explode(',', $skin["color"]) as $color) {
                                $payload .= urlencode(chr(intval($color) + 1));
                            }
                            $skinID = $skin["id"];
                            echo "<tr><td>
                                <div id=\"viewskin{$skinID}\" name=\"viewskin{$skinID}\" class=\"div_showskin\"
                                style=\"background-color:#FFFFFF; width:150px; height:150px; no-repeat center center fixed-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;\">
                                <embed type=\"application/x-shockwave-flash\" src=\"swfs/viewskin.swf?CACHE_VERSION=467\"
                                width=\"150\" height=\"150\" id=\"Skin-BBL-{$index}\" name=\"Skin-BBL-{$index}\" bgcolor=\"#FFFFFF\" quality=\"low\"
                                wmode=\"transparent\" menu=\"true\"
        flashvars=\"ACTION=40&amp;SKINID={$skinID}&amp;SKINCOLOR={$skin['color']}&amp;FONDID=0&amp;SHOWSKIN=1&amp;CACHE_VERSION=467&amp;HIDEBORDER=1\">
        </div>
        <script>
        var showSkin = new SWFObject(\"swfs/viewskin.swf?CACHE_VERSION=467\", \"Skin-BBL-{$index}\", \"250\", \"250\", \"64\", \"#FFFFFF\");
        showSkin.addParam('wmode', 'transparent');
        showSkin.addVariable('ACTION', '40');
        showSkin.addVariable('SKINID', '{$skinID}');
        showSkin.addVariable('SKINCOLOR', '{$skin['color']}');
        showSkin.addVariable('FONDID', '0');
        showSkin.addVariable('SHOWSKIN', '1');
        showSkin.addVariable('CACHE_VERSION', '467');
        showSkin.addVariable('HIDEBORDER', '1');
        showSkin.addParam('menu', 'true');
        showSkin.write('div_showskin');
        </script></td>
        <td>{$skin['name']}</td>
        <td>{$skin['comment']}</td>
        <td>{$skin['montant']}</td>
        <td><form method=\"POST\">
        <button class=\"btn btn-success\" type=\"submit\" name=\"buy\">Acheter</button>
        <input type=\"hidden\" name=\"skin-id\" value=\"{$skinID}\">
        </form></td></tr>";
                            $index++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <center>
                <?php skinBeforeAndNext($tableSkinIndex); ?>
            </center>
        <?php
        } else {
            if (isset($_POST['skin_query'])) {
                echo "<center><p>Aucun skin n'a été trouvé pour cette recherche.</p></center>";
            } else {
                echo "<center><p>Aucun skin n'est disponible ici.</p></center>";
            }
        }
        ?>
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <?php include_once("footer.php"); ?>

</body>

</html>