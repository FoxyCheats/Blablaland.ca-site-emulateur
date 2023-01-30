<?php
session_start();

include_once("backend/database.php");
include_once("backend/usercontroller.php");
include_once("backend/api.php");
include_once("backend/loto.php");
include_once("backend/reCaptcha.php");
$reCaptcha = new ReCaptcha("6LeNda8jAAAAAHC14p4JsFF24udseewYDwVrkw8E");
if (!isset($_SESSION['api_token'])) {
    return header("location: /404.php");
}

$account = getAccount($pdo, $_SESSION['api_token']);

if ($account['grade'] < 800) {
    return header("location: /404.php");
}

$result = [];

if (isset($_POST['init_loto'])) {
    if (isset($_POST['g-recaptcha-response'])) {
        $gRecaptchaResult = $_POST['g-recaptcha-response'];
        if ($reCaptcha->checkCode($gRecaptchaResult, getIp())) {
            if (isset($_POST['loto-label']) && isset($_POST['price']) && isset($_POST['play-price'])) {
                try {
                    $price = intval(trim(htmlspecialchars($_POST['price'])));
                    $lotoLabel = trim(htmlspecialchars($_POST['loto-label']));
                    $playPrice = intval(trim(htmlspecialchars($_POST['play-price'])));
                    if ($lotoLabel !== NULL && $price !== NULL && $playPrice !== NULL) {
                        if (strlen($lotoLabel) > 25) {
                            $result['lotolabel_too_long'] = "Il semblerait que le label soit trop long !";
                        } else {
                            $loto->init_loto($account['ID'], $lotoLabel, $price, $playPrice);
                        }
                    } else {
                        $result['fields_empty'] = "Il semblerait que vous n'ayez pas rempli les champs correctement !";
                    }
                } catch (\Throwable $th) {
                    $result['invalid_price'] = "Il semblerait que vous n'ayez pas entré une récompense correcte !";
                }
            } else {
                $result['fields_empty'] = "Il semblerait que vous n'ayez pas rempli tout le formulaire !";
            }
        } else {
            $result['invalid_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !";
        }
    } else {
        $result['empty_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !";
    }
}

if (isset($_POST['cancel']) || isset($_POST['finish'])) {
    if (isset($_POST['loto-id'])) {
        $lotoID = trim(htmlspecialchars($_POST['loto-id']));
        if (!empty($lotoID)) {
            if (isset($_POST['finish'])) {
                $winnerID = -1;
                $currentLoto = $loto->get_loto($lotoID);
                while ($winnerID == -1) {
                    $winnerID = $loto->declare_winner($lotoID);
                }
                $winner = getAccountWithID($pdo, $winnerID);
                $loto->assign_winner($winner["ID"], $lotoID);
                $loto->credit($winner, $currentLoto);
            } else if (isset($_POST['cancel'])) {
                $loto->cancel($lotoID);
            }
        } else {
            $result['empty_lotoid'] = "Une erreur s'est produite, rafraichissez la page !";
        }
    } else {
        $result['empty_lotoid'] = "Une erreur s'est produite, rafraichissez la page !";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once("head.php"); ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Blablaland - Loto</title>
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

    <div class="jumbotron container block">
        <form method="post">
            <h3 style="text-align: center;">Création d'un loto</h3>
            <hr>
            <div class="input-group input-group-lg">
                <span class="input-group-addon">
                    <i class="fa fa-pencil"></i>
                </span>
                <input class="form-control" type="text" name="loto-label" placeholder="Entrez le nom du loto" <?php if (isset($_POST['loto-label'])) echo 'value="' . $_POST['loto-label'] . '"'; ?> required>
                <small style="padding-bottom: 10px;">Maximum 25 caractères.</small>
            </div>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-gift"></i></span><input class="form-control" type="text" name="price" placeholder="Prix à gagner" <?php if (isset($_POST['price'])) echo 'value="' . $_POST['price'] . '"'; ?> required><small style="padding-bottom: 10px;">Uniquement des numéros.</small></div>
            <br>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-usd"></i></span><input class="form-control" type="text" name="play-price" placeholder="Somme à dépenser pour jouer (gratuit si vide)" <?php if (isset($_POST['play-price'])) echo 'value="' . $_POST['play-price'] . '"'; ?>></div>
            <br>
            <div style="text-align:center">
                <div class="row">
                    <div style="margin-left: calc(12.5% + 285px);" class="g-recaptcha" data-sitekey="6LeNda8jAAAAAGvn1cRMmzhxD4MtnYeoXcpyko0x"></div>
                    <br>
                    <input type="submit" name="init_loto" class="btn btn-lg btn-primary p-5" value="Créer un événement loto !">
                </div>
            </div>
        </form>
    </div>

    <div class="jumbotron container block">
        <form method="post">
            <h3 style="text-align: center;">Agenda des lotos</h3>
            <hr>

            <table class="table">
                <thead>

                    <tr>
                        <th scope="col" class="col-sm-2">Label du loto</th>
                        <th scope="col" class="col-sm-2">Créé par</th>
                        <th scope="col" class="col">Mise</th>
                        <th scope="col" class="col-sm-1">Récompense</th>
                        <th scope="col" class="col-sm-2">Gagné par</th>
                        <th scope="col" class="col">Participants</th>
                        <th scope="col" class="col-sm-1">Créé le</th>
                        <th scope="col" class="col-sm-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($loto->get_lotos()) as $req) {
                        if (!in_array($account["ID"], explode(",", $req['participants']))) {

                            $createdBy = getAccountWithID($pdo, $req['by']);
                            if (empty($req['won_by'])) {
                                $won_by = "-";
                            } else {
                                $won_by = $req['won_by'];
                            }
                            $participants = count(explode(",", $req['participants'])) - 1;
                            $author = $createdBy['login'];
                            if (!empty($createdBy['pseudo'])) {
                                $author .= " ({$createdBy['pseudo']})";
                            }
                            $date = date("d/m/Y à H:i:s", $req["launched_at"]);
                            echo "<tr>
                    <td>{$req['loto_label']}</td>
                    <td><a href=\"/members.php?id={$createdBy['ID']}\">{$author}</a></td>
                    <td>{$req['play_price']}</td>
                    <td>{$req['win_price']}</td>";

                            if ($won_by !== "-" && $won_by !== "canceled") {
                                $account = getAccountWithID($pdo, $won_by);
                                $won_by_display = $account['login'];
                                if (!empty($account['pseudo'])) {
                                    $won_by_display .= " ({$account['pseudo']})";
                                }
                                echo "<td><a href=\"/members.php?id={$account['ID']}\">{$won_by_display}</a></td>";
                            } else {
                                echo "<td>-</td>";
                            }

                            echo "<td>{$participants}</td>
                    <td>{$date}</td>";
                            if ($won_by === "-") {

                                echo "<td><form method=\"POST\"><div class=\"row\">
                        <div class=\"col-sm-4\">
                        <button class=\"btn btn-danger\" type=\"submit\" name=\"cancel\">Annuler</button>
                        </div>
                        <div class=\"col-sm-6\">
                        <button class=\"btn btn-primary\" type=\"submit\" name=\"finish\">Récompenser</button>
                        </div>
                        <input type=\"hidden\" name=\"loto-id\" value=\"{$req['id']}\">
                        </div></form></td>
    
                        </tr>";
                            } else if ($won_by === "canceled") {
                                echo "<td><small>Annulé</small></td></tr>";
                            } else {
                                echo "<td><small>Terminé</small></td></tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
    <?php include_once("footer.php"); ?>

</body>

</html>
