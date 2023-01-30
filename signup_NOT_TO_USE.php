<?php
session_start();

include_once("backend/usercontroller.php");
include_once("backend/database.php");

if (isset($_SESSION['api_token'])) {
    $isAPITokenValid = checkAPIToken($pdo, $_SESSION['api_token']);
    if (!$isAPITokenValid) {
        session_destroy();
    } else {
        header("location: /");
    }
}

$result = [];
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['conf-password']) && isset($_POST['email']) && isset($_POST['gender'])) {
    if (isset($_POST['g-recaptcha-response'])) {
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        if (!empty($gRecaptchaResponse)) {
            if (empty($_POST['rules']) || empty($_POST['cgu'])) {
                $result["tos_not_checked"] = "Vous devez accepter le règlement ainsi que les CGU et la politique de confidentialité avant de continuer !";
            } else {
                $result_ = signup($pdo, $_POST['username'], $_POST['password'], $_POST['conf-password'], $_POST['email'], $_POST['gender'], $gRecaptchaResponse, getIp());
                if ($result_ === 0) {
                    $result['success'] = "Un email de confirmation vient de vous être envoyé, aller voir vos messages, y compris dans les spams !";
                } else {
                    $result = $result_;
                }
            }
        } else {
            $result['empty_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !";
        }
    } else {
        $result['empty_recaptcha'] = "Confirmez que vous n'êtes pas un robot en effectuant le teste de reCaptcha !!";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Inscription</title>
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
        <form id="signup-form" method="POST" style="width: 400px; margin: 0 auto;">
            <h3 style="text-align: center;">Inscription</h3>
            <hr>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-user"></i></span><input autocomplete="off" class="form-control" type="text" name="username" placeholder="Nom d'utilisateur" <?php if (isset($_POST['username'])) echo 'value="' . $_POST['username'] . '"'; ?> required><small style="padding-bottom: 10px;">Maximum 25 caractères. (Essayez de le différencier de votre mot de passe)</small></div>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i style="position: relative; right:2px;" class="fa fa-envelope"></i></span><input autocomplete="off" class="form-control" type="email" name="email" placeholder="Adresse e-mail" <?php if (isset($_POST['email'])) echo 'value="' . $_POST['email'] . '"'; ?> required><small>Email de réinitialisation de mot de passe et confirmation d'IPs et de compte. (Essayez de la différencier de votre mot de passe)</small></div>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span><input autocomplete="off" class="form-control" type="password" name="password" placeholder="Mot de passe" required><small style="padding-bottom: 10px;">Minimum 6 caractères, différent de l'email et du nom d'utilisateur.</small></div>
            <br>
            <div class="input-group input-group-lg"><span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span><input autocomplete="off" class="form-control" name="conf-password" type="password" placeholder="Confirmation du mot de passe" required><small style="padding-bottom: 10px;">Doit correspondre au champs précédent.</small></div>
            <br>
            <div style="margin: 0 auto;" class="input-group input-group-lg">Tu es :<br><input type="radio" name="gender" value="1" <?php if (isset($_POST['genre']) && $_POST['genre'] === 1) echo "checked"; ?>>Un garçon &nbsp;&nbsp;<input type="radio" name="gender" value="2" <?php if (isset($_POST['gender']) && $_POST['gender'] === 2) echo "checked"; ?>>Une fille
                &nbsp;&nbsp;<input type="radio" name="gender" value="0" checked <?php if (isset($_POST['gender']) && $_POST['gender'] === 0) echo "checked"; ?>>Non précisé &nbsp;&nbsp;</div><br><input type="checkbox" name="rules" <?php if (isset($_POST['rules'])) echo "checked"; ?> required>J'accepte <a href="regles.php" target="_blank">le règlement de
                Blablaland.ca</a><br><input type="checkbox" name="cgu" <?php if (isset($_POST['cgu'])) echo "checked"; ?> required>J'accepte <a href="cgu.php" target="_blank">les
                conditions d'utilisation</a> et <a href="pdc.php" target="_blank">la politique de
                confidentialité</a><br>
            <br>
            <div style="text-align: center;">
                <div style="margin-left: 12.5%;" class="g-recaptcha" data-sitekey="6LeNda8jAAAAAGvn1cRMmzhxD4MtnYeoXcpyko0x"></div>
                <br>
                <input type="submit" class="btn btn-lg btn-primary" style="width: 75%;" value="Créer mon compte !"><br><a href="signin.php">Déjà inscrit ?</a>
            </div>
        </form>
    </div>
    <?php include_once("footer.php"); ?>
</body>

</html>
