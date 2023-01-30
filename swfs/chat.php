<?php
if (!isset($_SESSION)) {
    session_start();
}
include('../backend/usercontroller.php');

function seed($longueur = 10)
{
    $caracteres = '-_';
    $longueurMax = strlen($caracteres);
    $chaineAleatoire = '';
    for ($i = 0; $i < $longueur; $i++) {
        $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
    }
    return $chaineAleatoire;
}

function isSWF()
{
    $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;
    return (explode('/', $header)[0] == "ShockwaveFlash");
}

function isFromBBL()
{
    return strpos($_SERVER['HTTP_REFERER'], "/play.php");
}


if ($logged && isSWF() && isFromBBL()) {
    header("Content-type: application/x-shockwave-flash");
    $chat = file_get_contents('chat.swf');
    $header = substr($chat, 0, 8);
    $content = substr($chat, 8);
    $content = gzuncompress($content);
    $base = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
    $token = seed(strlen($base));
    $result = "UPDATE chat SET token=? WHERE login=?";
    $stmt = $pdo->prepare($result);
    $stmt->execute([$token, $_SESSION['login']]);
    $content = str_replace($base, $token, $content);
    $content = gzcompress($content);
}

