<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once('../../backend/database.php');
include_once('../../backend/api.php');

if (isset($_SESSION['api_token'])) {
    echo "RESULT=1";
    $account = getAccount($pdo, $_SESSION['api_token']);
    $accountLogin = $account['login'];
    $skins = [];
    $skinsArray = explode(",", $account['skinsList']);
    array_pop($skinsArray);
    foreach ($skinsArray as $skin) {
        array_push($skins, getSkin($pdo, $skin));
    }
    $i = 0;
    foreach ($skins as $data) {
        if (($data["disponible"] == "1" || in_array($data['id'], $skinsArray)) && file_exists("../../data/skin/{$data['id']}/skin.swf")) {
            echo "&SKID_$i=" . urlencode($data['id']);
            echo "&SKCOM_$i=" . urlencode(decode($data["comment"]));
            echo "&SKNAME_$i=" . urlencode(decode($data["name"]));
            echo "&SKADDON_$i=" . urlencode($data["addon"]);
            echo "&SKCOL_$i=" . urlencode($data["color"]);
            echo "&SKLASTCOL_$i=" . urlencode($data["color"]);
            echo "&SKFAVORI_$i=" . urlencode($data["favori"]);
            $i++;
        }
    }

    echo "&SKINID=" . $account["skinid"] . "&SKINCOLOR=" . encode($account["skincolors"]) . "&NB=$i";
} else {
    echo "RESULT=0";
}

function encode($data)
{
    $colors = explode(',', $data);
    $res = "";

    foreach ($colors as $color) {
        $res .= chr($color + 1);
    }

    return $res;
}

function decode($string)
{
    $string = str_replace('&agrave;', 'à', $string);
    $string = str_replace('&egrave;', 'è', $string);
    $string = str_replace('&eacute;', 'é', $string);
    $string = str_replace('&ecirc;', 'ê', $string);
    $string = str_replace('&acirc;', 'â', $string);

    return $string;
}

