<?php
session_start();

include_once('backend/database.php');
include_once('backend/usercontroller.php');

$result = [];
if (isset($_POST['people'])) {
    $people = trim(htmlspecialchars($_POST['people']));
    if (empty($people)) {
        $result['empty_pseudo'] = "Vous devez entrer un pseudonyme à rechercher !";
    } else {
        $reqPeopleData = $pdo->prepare("SELECT * FROM users WHERE pseudo LIKE '$people%'");
        $reqPeopleData->execute();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>

    <title>Blablaland - Annuaire</title>
</head>

<body>
    <style type="text/css">
        th {
            border-bottom: 3px solid gray;
        }

        table,
        th,
        td {
            padding: 20px;
            text-align: center;
            margin: 0 auto;
            width: 50%;
        }

        td {
            border-bottom: 1px solid gray;
        }
    </style>
    <?php include_once("header.php"); ?>
    <div class="container">
        <div class="jumbotron">
            <h3>Annuaire</h3>

            <p>A la conquête de Blablateurs
                Tu cherches un Blabla en particulier ? Pour en savoir un peu plus sur lui, ou juste pour rencontrer de nouveaux amis pour tchater avec eux, fais une recherche ici pour voir qui est qui, et qui fait quoi dans ce joli petit monde qu'est le jeu de tchat de Blablaland.
            </p>

            <form method="POST">
                <label>Rechercher</label><br>
                <input type="text" name="people" style="padding: 5px; border:1px solid black; border-radius: 5px;" class="mr-5" placeholder="Pseudo du joueur">
                <input class="btn btn-primary p-5 ml-3" type="submit" name="research" value="Rechercher !">
            </form>


            <div id="results">
                <?php
                if (isset($reqPeopleData) && empty($reqPeopleData)) {
                    ?><center><p>Aucun utilisateur trouvé</p></center><?php
                } else if (isset($reqPeopleData)) {
                ?><table class="table">
                        <thead>
                            <th>Login</th>
                            <th>Pseudo</th>
                        </thead>

                        <?php

                        while ($data = $reqPeopleData->fetch()) {
                        ?>
                            <tr>

                                <td><a href="members.php?id=<?= $data['ID']; ?>"><?= $data['login']; ?></a></td>
                                <td><a href="members.php?id=<?= $data['ID']; ?>"><?= $data['pseudo']; ?></a></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table><?php
                        } else if (!empty($result)) {
                            ?><br><div class="errors bg-danger alert">
                        <ul>
                            <?php foreach ($result as $error) {
                            ?>
                                <li><?= $error ?></li>
                            <?php
                            } ?>
                        </ul>
                    </div><?php
                        }
                            ?>
            </div>
        </div>

    </div>
    <?php include_once("footer.php"); ?>

</body>

</html>