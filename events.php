<?php
session_start();

include_once("backend/loto.php");
include_once("backend/database.php");
include_once("backend/api.php");

if (!isset($_SESSION['api_token'])) {
    return header('location: /signin.php');
}

$account = getAccount($pdo, $_SESSION['api_token']);
$result = [];
if (isset($_POST['participate'])) {
    if (isset($_POST['event-id'])) {
        try {
            $lotoID = intval(trim(htmlspecialchars($_POST['event-id'])));
            if (!empty($lotoID)) {
                $loto->participate($lotoID, $account['ID']);
            } else {
                $result['empty_eventid'] = "Un erreur s'est produite, rafraichissez la page et réessayez !";
            }
        } catch (\Throwable $th) {
            $result['invalid_eventid'] = "Il semble que l'identifiant de l'événement soit invalide, rafraichissez la page et réessayez !";
        }
    } else {
        $result['empty_eventid'] = "Un erreur s'est produite, rafraichissez la page et réessayez !";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Événements</title>
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
            <h3 style="text-align: center;">Liste des événements disponibles</h3>
            <hr>
            <?php $lotos = array_reverse($loto->get_lotos());
            $valid_lotos = 0;
            foreach ($lotos as $loto_) {
                if (empty($loto_['won_by']) && !in_array($account['ID'], explode(",", $loto_['participants']))) {
                    $valid_lotos++;
                }
            }
            if ($valid_lotos > 0) { ?>

                <table class="table">
                    <thead>

                        <tr>
                            <th scope="col" class="col-sm-2">Label du loto</th>
                            <th scope="col" class="col-sm-2">Créé par</th>
                            <th scope="col" class="col">Mise</th>
                            <th scope="col" class="col-sm-1">Récompense</th>
                            <th scope="col" class="col">Participants</th>
                            <th scope="col" class="col-sm-2">Créé le</th>
                            <th scope="col" class="col-sm-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotos as $req) {
                            if (!in_array($account['ID'], explode(",", $req['participants']))) {
                                $createdBy = getAccountWithID($pdo, $req['by']);
                                if (empty($req['won_by'])) {
                                    $participants = count(explode(",", $req['participants'])) - 1;
                                    $date = date("d/m/Y à H:i:s", $req["launched_at"]);
                                    $author = $createdBy['login'];
                                    if (!empty($createdBy['pseudo'])) {
                                        $author .= " ({$createdBy['pseudo']})";
                                    }
                                    echo "<tr>
                        <td>{$req['loto_label']}</td>
                        <td><a href=\"/members.php?id={$createdBy['ID']}\">{$author}</a></td>
                        <td>{$req['play_price']}</td>
                        <td>{$req['win_price']}</td>
                        <td>{$participants}</td>
                        <td><small>{$date}</small></td>
                        <td><form method=\"POST\">
                            <div>
                                <button class=\"btn btn-success\" type=\"submit\" name=\"participate\">Participer ({$req['play_price']} BBL)</button>
                                <input type=\"hidden\" name=\"event-id\" value=\"{$req['id']}\">
                            </div>
                        </form></td>
                        </tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <center>
                    <p>Aucun nouvel événement n'est en cours.</p>
                </center>

            <?php } ?>
        </form>
    </div>

    <div class="jumbotron container block">
        <form method="post">
            <h3 style="text-align: center;">Liste des événements auxquels vous participez</h3>
            <hr>
            <?php $lotos = array_reverse($loto->get_lotos());
            $valid_lotos = 0;
            foreach ($lotos as $loto_) {
                if (empty($loto_['won_by']) && in_array($account['ID'], explode(",", $loto_['participants']))) {
                    $valid_lotos++;
                }
            }
            if ($valid_lotos > 0) { ?>

                <table class="table">
                    <thead>

                        <tr>
                            <th scope="col" class="col-sm-2">Label du loto</th>
                            <th scope="col" class="col-sm-2">Créé par</th>
                            <th scope="col" class="col">Mise</th>
                            <th scope="col" class="col-sm-1">Récompense</th>
                            <th scope="col" class="col">Participants</th>
                            <th scope="col" class="col-sm-2">Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotos as $req) {
                            $createdBy = getAccountWithID($pdo, $req['by']);
                            if (empty($req['won_by'])) {
                                $participants = count(explode(",", $req['participants'])) - 1;
                                $date = date("d/m/Y à H:i:s", $req["launched_at"]);
                                $author = $createdBy['login'];
                                if (!empty($createdBy['pseudo'])) {
                                    $author .= " ({$createdBy['pseudo']})";
                                }
                                echo "<tr>
                    <td>{$req['loto_label']}</td>
                    <td><a href=\"/members.php?id={$createdBy['ID']}\">{$author}</a></td>
                    <td>{$req['play_price']}</td>
                    <td>{$req['win_price']}</td>
                    <td>{$participants}</td>
                    <td><small>{$date}</small></td>
                    </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <center>
                    <p>Vous ne participez à aucun événement.</p>
                </center>

            <?php } ?>
        </form>
    </div>


    <div class="jumbotron container block">
        <h3 style="text-align: center;">Liste des événements passés</h3>
        <hr>
        <?php $lotos = array_reverse($loto->get_lotos());
        if (count($lotos) > 0) { ?>
            <table class="table">
                <thead>

                    <tr>
                        <th scope="col" class="col-sm-2">Label du loto</th>
                        <th scope="col" class="col-sm-2">Créé par</th>
                        <th scope="col" class="col">Mise</th>
                        <th scope="col" class="col-sm-1">Récompense</th>
                        <th scope="col" class="col">Participants</th>
                        <th scope="col" class="col-sm-2">Créé le</th>
                        <th scope="col" class="col-sm-2">Gagné par</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lotos as $req) {
                        $createdBy = getAccountWithID($pdo, $req['by']);
                        $wonby = getAccountWithID($pdo, $req['won_by']);
                        if (!empty($wonby)) {
                            $participants = count(explode(",", $req['participants'])) - 1;
                            $date = date("d/m/Y à H:i:s", $req["launched_at"]);
                            $author = $createdBy['login'];
                            if (!empty($createdBy['pseudo'])) {
                                $author .= " ({$createdBy['pseudo']})";
                            }
                            echo "<tr>
                    <td>{$req['loto_label']}</td>
                    <td><a href=\"/members.php?id={$createdBy['ID']}\">{$author}</a></td>
                    <td>{$req['play_price']}</td>
                    <td>{$req['win_price']}</td>
                    <td>{$participants}</td>
                    <td><small>{$date}</small></td>
                    <td><small><a href=\"/members.php?id={$wonby['ID']}\" target=\"_BLANK\">{$wonby['login']} ({$wonby['pseudo']})</a></small></td>
                    </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <center>
                <p>Il n'y a aucun événement de passé pour le moment.</p>
            </center>

        <?php } ?>
    </div>

    <br><br><br><br><br>
    <?php include_once("footer.php"); ?>

</body>

</html>