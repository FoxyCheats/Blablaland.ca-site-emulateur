<?php
include_once("usercontroller.php");
include_once("database.php");

function checkAPIToken(\PDO $pdo, $apiToken)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE api_token = ? ");
    $req->execute([$apiToken]);
    $response = $req->fetch();
    return gettype($response) == "array" && !empty($response);
}

function genAPIToken($username, $password, $email)
{
    return hash("sha512", $username . $email . $password . time());
}

function getAccount(\PDO $pdo, $apiToken)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE api_token = ? ");
    $req->execute([$apiToken]);
    $response = $req->fetch();
    if (!empty($response)) {
        $response['email'] = uncipherEmail($response['email']);
    }
    return $response;
}

function getAccountWithLogin(\PDO $pdo, $login)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE login = ? ");
    $req->execute([$login]);
    $response = $req->fetch();
    if (!empty($response)) {
        $response['email'] = uncipherEmail($response['email']);
    }
    return $response;
}

function getAccountWithEmail(\PDO $pdo, $email)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE email = ? ");
    $req->execute([$email]);
    $response = $req->fetch();
    if (!empty($response)) {
        $response['email'] = uncipherEmail($response['email']);
    }
    return $response;
}

function getAccountWithID(\PDO $pdo, $id)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE id = ? ");
    $req->execute([$id]);
    $response = $req->fetch();
    if (!empty($response)) {
        $response['email'] = uncipherEmail($response['email']);
    }
    return $response;
}

function getBadge(\PDO $pdo, $badgeID)
{
    $req = $pdo->prepare("SELECT * FROM badges WHERE id=?");
    $req->execute([$badgeID]);
    return $req->fetch();
}

function getSkin(\PDO $pdo, $skinID)
{
    $req = $pdo->prepare("SELECT * FROM skins WHERE id=?");
    $req->execute([$skinID]);
    return $req->fetch();
}

