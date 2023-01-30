<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once('../../backend/database.php');
include_once('../../backend/usercontroller.php');

if (isset($_SESSION['api_token'])) {
    echo "RESULT=1";
    $account = getAccount($pdo, $_SESSION['api_token']);
    $login = $account['login'];

    $skinID = query("SKINID");
    $req = $pdo->query("SELECT * FROM skins WHERE id = $skinID");
    $data = $req->fetch();
    if (($data["disponible"] == "1" || in_array($data["id"], explode(",", $account['skinsList']))) && file_exists("../../data/skin/{$data['id']}/skin.swf")) {
        $login = $account['login'];
        $req = $pdo->prepare("SELECT * FROM skin WHERE skinid=:skinID AND login=:login");
        $req->bindParam(":skinID", $skinID);
        $req->bindParam(":login", $login);
        $req->execute();
        $res = $req->fetch();
        $new = empty($res);
        if ($new == true) {
            $insertQuery = "INSERT INTO skin (color, skinid, login) VALUES (?, ?, ?)";
            $req = $pdo->prepare($insertQuery);
            $req->execute([query("SKINCOLOR"), query("SKINID"), $account['login']]);
        } else {
            $insertQuery = "UPDATE skin SET color=? WHERE skinid=? AND login = $login";
            $req = $pdo->prepare($insertQuery);
            $req->execute([query("SKINCOLOR"), query("SKINID")]);
        }
        $update = "UPDATE users SET skincolors=?, skinid=? WHERE login=?";
        $req = $pdo->prepare($update);
        $req->execute([convertToArray(query("SKINCOLOR")), query("SKINID"), $account['login']]);
    }
} else {
    echo "RESULT=0";
}

function query($data)
{
    if (isset($_GET[$data])) {
        return $_GET[$data];
    }
    return null;
}

function decode($data)
{
    $colors = str_split(urldecode($data));
    $res = "";

    foreach ($colors as $color) {
        $res .= chr($color - 1);
    }

    return $res;
}

function convertToArray($data)
{
    $color = array_map("ord", str_split($data));

    foreach ($color as $i => $v) {
        $valeur = $v - 1;
        $color[$i] = $valeur;
    }

    return implode(",", $color);
}

