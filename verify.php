<?php
session_start();
include_once("backend/database.php");
include_once("backend/usercontroller.php");

if (isset($_GET['email_token'])) {
    $emailToken = trim(htmlspecialchars($_GET['email_token']));
    $account = getAccount($pdo, $emailToken);
    if ($account['email_verified'] === true) {
        return header("location: /404.php");
    }
    acceptEmail($pdo, $account['email']);
    return header("location: /signin.php");
} else if (isset($_GET['ip_token']) && isset($_GET['token'])) {
    $ipToken = trim(htmlspecialchars($_GET['ip_token']));
    $apiToken = trim(htmlspecialchars($_GET['token']));
    $account = getAccount($pdo, $apiToken);
    acceptIP($pdo, $ipToken, $apiToken);
    header("location: /signin.php");
} else if (isset($_POST['reset_password'])) {
    if (isset($_POST['new-password']) && isset($_POST['conf-new-password'])) {
        $apiToken = trim(htmlspecialchars($_GET['reset_password']));
        $account = getAccount($pdo, $apiToken);
        if (empty($account)) {
            header("location: /404.php");
        }
        $newPassword = $_POST['new-password'];
        $confNewPassword = $_POST['conf-new-password'];
        if ($newPassword === $confNewPassword) {
            $newPassword = password_hash($_POST['new-password'], PASSWORD_BCRYPT);
            updateAccount($pdo, $account['password'], $newPassword, "password", $account["api_token"]);
            $apiToken = genAPIToken($account['username'], $newPassword, $account['email']);
            updateAccount($pdo, $account['api_token'], $apiToken, "api_token", $account["api_token"]);
            return header("location: /signin.php");
        } else {
            $result['invalid_password_confirmation'] = "Le nouveau mot de passe ne correspond pas à sa confirmation !";
        }
    } else {
        $result['empty_fields'] = "Certains champs sont incomplets !";
    }
} else if (isset($_GET['reset_password'])) {
    $apiToken = trim(htmlspecialchars($_GET['reset_password']));
    $account = getAccount($pdo, $apiToken);
    if (empty($account)) {
        header("location: /404.php");
    }
?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <?php include_once("head.php"); ?>
        <title>Blablaland - Choissiez votre nouveau mot de passe</title>

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
                <h3 style="text-align: center;">Changement du mot de passe</h3>
                <hr>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <i class="fa fa-unlock"></i>
                    </span>
                    <input class="form-control" type="password" name="new-password" placeholder="Nouveau mot de passe">
                    <small>Évitez un lien entre votre adresse mail/nom d'utilisateur et votre nouveau mot de passe.</small>
                </div><br>

                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <i class="fa fa-unlock"></i>
                    </span>
                    <input class="form-control" type="password" name="conf-new-password" placeholder="Confirmation du nouveau mot de passe">
                    <small>Doit correspondre au champs précédent.</small>
                </div><br>
                <center>
                    <button type="submit" name="reset_password" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> APPLIQUER LES CHANGEMENTS</button>

                </center>
            </form>
        </div>
        <?php include_once("footer.php"); ?>

    </body>

    </html>
<?php
} else {
    header("location: /404.php");
}
