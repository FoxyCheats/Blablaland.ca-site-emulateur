<?php
session_start();

include_once("backend/database.php");
include_once("backend/usercontroller.php");

if (isset($_SESSION['api_token'])) {
    header("location: /");
}

$result = [];
if (isset($_POST['username']) && isset($_POST['password'])) {
    if (isset($_POST['g-recaptcha-response'])) {
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        if (!empty($gRecaptchaResponse)) {

            $result = signin($pdo, $_POST['username'], $_POST['password'], $gRecaptchaResponse, getIp());
            if ($result === 0) {
                header("location: /");
            }
        } else {
            $result['empty_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !";
        }
    } else {
        $result['empty_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Connexion</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>
    <?php
    include_once("header.php");

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
    <div class="container">
        <form id="signin-form" method="POST" style="width: 400px; margin: 0 auto;">
            <h3 style="text-align: center;">Connexion</h3>
            <hr>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-user"></i></span><input class="form-control" type="text" name="username" placeholder="Nom d'utilisateur" <?php if (isset($_POST['username'])) echo 'value="' . $_POST['username'] . '"'; ?> required maxlength="10"></div>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span><input class="form-control" type="password" name="password" placeholder="Mot de passe" required></div>
            <br>
            <div style="text-align: center;">
                <div style="margin-left: 12.5%;" class="g-recaptcha" data-sitekey="6LeNda8jAAAAAGvn1cRMmzhxD4MtnYeoXcpyko0x"></div>

                <br> <input type="submit" class="btn btn-lg btn-primary" value="Connexion !" style="width: 75%;"><br><a href="signup.php">Pas encore inscrit ?</a><br><a href="reset_password.php">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>

    <?php include_once("footer.php"); ?>
</body>

</html>
