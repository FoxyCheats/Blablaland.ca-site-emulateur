<?php
include_once("api.php");
include_once("reCaptcha.php");
include_once("database.php");

if (!isset($_SESSION)) {
    session_start();
}
$logged = isset($_SESSION['session']);
$chatColumns = [];



if ($logged) {
    $getUser = $pdo->prepare("SELECT * FROM users WHERE session = :session");
    $getUser->bindValue(":session", $_SESSION['session']);
    $getUser->execute();
    if ($getUser->rowCount() > 0) {
        $userColumns = $getUser->fetch(\PDO::FETCH_ASSOC);
    }

    if (count($userColumns) == 0) {
        header("location: /disconnect.php");
    }

    $getUser = $pdo->prepare("SELECT * FROM chat WHERE login = :login");
    $getUser->bindValue(":login", $userColumns['login']);
    $getUser->execute();
    if ($getUser->rowCount() > 0) {
        $chatColumns = $getUser->fetch(\PDO::FETCH_ASSOC);
    }
}

function genHeadersEmail($email)
{
    $headers = "Reply-To: no-reply@blablaland.online\r\n";
    $headers .= "Return-Path: Blablaland.online <no-reply@blablaland.online>\r\n";
    $headers .= "From: Blablaland.online <no-reply@blablaland.online>\r\n";
    $headers .= "Organization: Blablaland.online\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
    $headers .= "CC: {$email}\r\n";
    return $headers;
}

function genEmailBody($content)
{
    $body = "<!DOCTYPE html>
    <html lang=\"fr\">
    <body>
        <div style=\"padding: 24px 48px; background-color: #ccc; color: rgb(25,25,25);\">
            <p>{$content}</p>
        </div>
    </body>
    </html>";
    return $body;
}

function emailAlreadyTaken(\PDO $pdo, $email)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE email = $email");
    $res = $req->fetch();
    if (!empty($res) && $res['email_verified'] === true) {
        return true;
    }
    return false;
}

function getIp()
{
    $ip = "void";
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }

    if (empty($ip)) {
        $ip = "void";
    }
    return $ip;
}

function usernameAlreadyTaken(\PDO $pdo, $username)
{
    $req = $pdo->prepare("SELECT * FROM users WHERE login = $username");
    $res = $req->fetch();
    if (!empty($res) && isset($res['api_token'])) {
        return true;
    }
    return false;
}

function acceptEmail(\PDO $pdo, $email)
{
    $account = getAccountWithEmail($pdo, cipherEmail($email));
    return updateAccount($pdo, 0, true, "email_verified", $account['api_token']);
}

function acceptIP(\PDO $pdo, $ip, $apiToken)
{
    $account = getAccount($pdo, $apiToken);
    return updateAccount($pdo, 0, $account['ip'] . $ip, "ip", $account['api_token']);
}

function emailVerification($email, $apiToken)
{
    $message = genEmailBody("Voici votre code de vérification de compte pour {$_SERVER['SERVER_NAME']}:<br><br>Rendez-vous sur <a href=\"https://{$_SERVER['SERVER_NAME']}/verify.php?email_token={$apiToken}\">ce lien</a> pour confirmer votre email.<br><br>Si vous n'êtes pas l'auteur(e) de cette action, ignorez ce message.");
    $result = mail($email, "Vérification de compte {$_SERVER['SERVER_NAME']}", $message, genHeadersEmail($email));
    if (!$result) {
        return ['invalid_email' => "L'adresse mail est invalide !"];
    }
    return 0;
}

function ipVerification($email, $ip, $apiToken)
{
    $message = genEmailBody("Il semblerait que quelqu'un se connecte via un autre appareil que celui utilisé pour l'enregistrement du compte. S'agit-il de vous ? Si oui, merci de cliquer sur <a href=\"https://{$_SERVER['SERVER_NAME']}/verify.php?ip_token={$ip}&token={$apiToken}\">ce lien</a>. Si non, ignorez ce message, votre compte est protégé.");
    $result = mail($email, "Connexion suspicieuse {$_SERVER['SERVER_NAME']}", $message, genHeadersEmail($email));
    if (!$result) {
        return ['invalid_email' => "L'adresse mail est invalide, aucune confirmation d'IP ne peut être effectuée. Merci de contacter un administrateur !"];
    }
    return 0;
}

function signup(\PDO $pdo, $username, $password, $confPassword, $email, $gender, $gRecaptchaResponse, $ip)
{
    $reCaptcha = new ReCaptcha('6LdQcq8jAAAAABoBSpPJowbqcC89nfeSzEDvWVzV');
    if ($reCaptcha->checkCode($gRecaptchaResponse, $ip)) {
        $req = $pdo->prepare("SELECT * FROM users WHERE api_token = ? ");
        $req->execute([$apiToken]);
        $response = $req->fetch();
        $username = trim(htmlspecialchars($username));
        $email = cipherEmail(trim(htmlspecialchars($email)));
        $gender = trim(htmlspecialchars($gender));
        $errors = checkDetailsForSignup($response, $username, $password, $confPassword, $email, $gender);
        $password = password_hash($password, PASSWORD_BCRYPT);
        $ip = hash("sha512", $ip);
        if (count($errors) === 0) {
            try {
                $chatRegister = $pdo->prepare("INSERT INTO chat (login) VALUES (?)");
                $chatRegister->execute([$username]);
                $apiToken = genAPIToken($username, $password, $email);
                $req = $pdo->prepare("INSERT INTO users (login, pseudo, password, email, api_token, genre, registerdate, ip, skinid, skinsList) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $req->execute([$username, $username, $password, $email, $apiToken, $gender, time(), $ip . ",", "7", "1,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,"]);
                $validEmail = emailVerification(uncipherEmail($email), $apiToken);
                return $validEmail;
            } catch (\Exception $e) {
                return 1;
            }
        } else {
            return $errors;
        }
    } else {
        return ['invalid_recaptcha' => "Nous pensons que vous êtes un robot, remplissez correctement le reCaptcha !"];
    }
}

function cipherEmail($email)
{
    return bin2hex(base64_encode($email));
}

function uncipherEmail($email)
{
    return base64_decode(hex2bin($email));
}

function signin(\PDO $pdo, $username, $password, $gRecaptchaResponse, $ip)
{
    $username = trim(htmlspecialchars($username));
    $errors = checkDetailsForSignin($username, $password);
    $reCaptcha = new ReCaptcha('6LeNda8jAAAAAHC14p4JsFF24udseewYDwVrkw8E');
    if ($reCaptcha->checkCode($gRecaptchaResponse, $ip)) {

        if (count($errors) === 0) {
            $hashedIP = hash("sha512", $ip);
            try {
                $req = $pdo->prepare("SELECT * FROM users WHERE `login` = ?");
                $req->execute([$username]);
                $result = $req->fetch();
                if (!empty($result)) {

                    if (password_verify($password, $result['password'])) {
                        if (empty(explode(",",  $result['ip'])[$hashedIP])) {
                            if (!isset($_SESSION)) {
                                session_start();
                            }
                            $_SESSION['api_token'] = $result['api_token'];
                            $_SESSION['login'] = $result['login'];
                            $_SESSION['bbl'] = $result['bbl'];
                            $_SESSION['xp'] = $result['xp'];
                            $_SESSION["skinaction"] = $result["skinaction"];
                            $_SESSION["skinid"] = $result["skinid"];
                            $_SESSION["skincolors"] = $result["skincolors"];
                            $_SESSION["show_skin"] = $result["show_skin"];
                            $_SESSION["ID"] = $result["ID"];
                            $_SESSION["friends"] = explode(",", $result["amis"]);
                            $_SESSION["session"] = $result["session"];

                            updateAccount($pdo, 0, $result['api_token'], "session", $result['api_token']);
                            return 0;
                        } else {
                            ipVerification(uncipherEmail($result['email']), $hashedIP, $result['api_token']);
                            return ['suspicious_ip' => "L'adresse IP semble différente de celle de votre inscription, merci de confirmer la connexion via votre adresse mail."];
                        }
                    } else {
                        return ['invalid_password' => "Les informations soumises sont invalides !"];
                    }
                } else {
                    return ['invalid_username' => "Les informations entrées semblent invalides !"];
                }
            } catch (\Exception $e) {
                return 1;
            }
        } else {
            return $errors;
        }
    } else {
        return ['invalid_recaptcha' => "Nous pensons que vous êtes un robot, remplissez correctement le reCaptcha !"];
    }
}

function checkBanIP(\PDO $pdo, $ip)
{
    $ip = hash("sha512", $ip);
    $req = $pdo->query("SELECT * FROM banip WHERE ip = $ip");
    return $req->rowCount() > 0;
}

function checkDetailsForSignup(\PDO $pdo, $username, $password, $confPassword, $email, $gender)
{
    $errors = [];
    $authorizedAlphabet = explode(",", "a,z,e,r,t,y,u,i,o,p,q,s,d,f,g,h,j,k,l,m,w,x,c,v,b,n,0,1,2,3,4,5,6,7,8,9,A,Z,E,R,T,Y,U,I,O,P,Q,S,D,F,G,H,J,K,L,M,,W,X,C,V,B,N,-,_");
    if (empty($username)) {
        $errors['empty_username'] = "Le nom d'utilisateur ne peut pas être vide !";
    } else if ($username === $password) {
        $errors['username_equals_password'] = "Le mot de passe ne doit pas être lié à votre nom d'utilisateur !";
    } else if (strlen($username) > 25) {
        $errors['username_too_long'] = "Le nom d'utilisateur ne doit pas dépasser 25 caractères !";
    } else if (empty($email)) {
        $errors['empty_email'] = "L'adresse mail ne peut pas être vide !";
    } else if (!filter_var(uncipherEmail($email), FILTER_VALIDATE_EMAIL)) {
        $errors['invalid_email'] = "L'adresse mail est invalide !";
    } else if (explode("@", $email)[0] === $password) {
        $errors['email_equals_password'] = "Le mot de passe ne doit pas être lié à votre adresse mail !";
    } else if (strlen($password) < 6) {
        $errors['password_too_short'] = "Le mot de passe doit contenir au moins 6 caractères !";
    } else if ($password !== $confPassword) {
        $errors['different_passwords'] = "Le mot de passe et la confirmation ne correspondent pas !";
    } else if ($gender < 0 || $gender > 3) {
        $errors['invalid_gender'] = "Le sexe entré n'est pas prit en charge. Rafraichissez la page puis reéssayez !";
    } else if (emailAlreadyTaken($pdo, $email)) {
        $errors['email_already_used'] = "L'adresse mail est déjà utilisée !";
    } else if (usernameAlreadyTaken($pdo, $username)) {
        $errors['username_already_used'] = "Le nom d'utilisateur est déjà utilisé !";
    }

    foreach ($username as $char) {
        if (!in_array($char, $authorizedAlphabet)) {
            $errors['invalid_char_username'] = "Certians caractères dans votre pseudo ne sont pas tolérés (caractères alphanumériques, tirets et underscore uniquement) !";
        }
    }

    return $errors;
}

function checkDetailsForSignin($username, $password)
{
    $errors = [];
    if (empty(trim($username))) {
        $errors['empty_username'] = "Le nom d'utilisateur ne peut pas être vide !";
    } else if ($username === $password || strlen($password) < 6) {
        $errors['bad_credits'] = "Informations invalides !";
    }

    return $errors;
}

function updateAccount(\PDO $pdo, $tmp, $after, $column, $apiToken)
{
    $req = $pdo->prepare("UPDATE users SET $column = ? WHERE api_token = ?");
    $req->execute([$after, $apiToken]);
    $req->rowCount();
    return 0;
}

function askForResetPassword(\PDO $pdo, $email, $gRecaptchaResponse)
{
    $reCaptcha = new ReCaptcha('6LdQcq8jAAAAABoBSpPJowbqcC89nfeSzEDvWVzV');
    if ($reCaptcha->checkCode($gRecaptchaResponse, getIp())) {
        $account = getAccountWithEmail($pdo, cipherEmail($email));
        if (!empty($account)) {
            $message = genEmailBody("Voici <a href=\"https://{$_SERVER['SERVER_NAME']}/verify.php?reset_password={$account['api_token']}\">un lien</a> pour réinitialiser votre mot de passe. Si vous n'avez pas souhaité cette action, restez tranquille, votre compte est protégé.");
            $result = mail($email, "Réinitialisation du mot de passe {$_SERVER['SERVER_NAME']}", $message, genHeadersEmail($email));
            if (!$result) {
                return ['invalid_email' => "L'adresse mail sensée recevoir le lien de réinitialisation n'est pas joignable !"];
            }
            return [];
        }
        return ['invalid_email' => "L'adresse mail entrée n'est associée à aucun compte !"];
    } else {
        return ['invalid_recaptcha' => "Vous devez effectuer le teste de reCaptcha pour continuer !"];
    }
}

function updatePassword(\PDO $pdo, $apiToken, $password)
{
    updateAccount($pdo, null, $password, "password", $apiToken);
    return true;
}
