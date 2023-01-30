<?php
session_start();
include_once("backend/usercontroller.php");
include_once("backend/api.php");
include_once("backend/reCaptcha.php");
include_once("backend/database.php");

$result = [];
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['email'])) {
    if (isset($_POST['g-recaptcha-response'])) {
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        if (!empty($gRecaptchaResponse)) {

            $result = askForResetPassword($pdo, trim(htmlspecialchars($_POST['email'])), $gRecaptchaResponse);
            if ($result === 0) {
                $result['email_sent'] = "Un email de réinitialisation de mot de passe vient de vous être envoyé !";
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
    <title>Blablaland - Réinitialisation de mot de passe</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
        <form id="signin-form" method="POST" style="width: 400px; margin: 0 auto;">
            <h3 style="text-align: center;">Réinitialisation de mot de passe</h3>
            <hr>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-user"></i></span><input class="form-control" type="text" name="email" placeholder="Adresse mail du compte" <?php if (isset($_POST['email'])) echo 'value="' . $_POST['email'] . '"'; ?> required></div>
            <br>
            <div style="text-align: center;">
                <div style="margin-left: 12.5%;" class="g-recaptcha" data-sitekey="6LeNda8jAAAAAGvn1cRMmzhxD4MtnYeoXcpyko0x"></div>
                <br> <input type="submit" class="btn btn-lg btn-primary" value="Réinitialiser mon mot de passe !">
            </div>
        </form>
    </div>
    <?php include_once("footer.php"); ?>

</body>

</html>