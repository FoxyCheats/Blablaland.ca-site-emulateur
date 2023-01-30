<?php
session_start();
include_once('backend/database.php');

$resNews = [];

if (isset($_GET['id'])) {
    $id = trim(htmlspecialchars($_GET['id']));
    try {
        $id = intval($id);
        $reqNews = $pdo->query("SELECT * FROM news WHERE id = $id");
        $resNews = $reqNews->fetch();
    } catch (\Exception $e) {
        $resNews = [];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - News</title>
</head>

<body>
    <?php include_once("header.php"); ?>

    <div class="container">
        <div class="jumbotron">
            <?php
            if (empty($resNews)) {
            ?>
                <p>Article introuvable.</p>
            <?php
            } else { ?>
                <h1><?= $resNews['titre']; ?></h1>

                <p><?= $resNews['contenu']; ?></p>

            <?php }
            ?>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br><br><br>
    <br>
    <?php
    include_once('footer.php');
    ?>
</body>

</html>
