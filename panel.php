<?php
session_start();

include_once("backend/database.php");
include_once("backend/usercontroller.php");
include_once("backend/api.php");

if (empty($_SESSION['api_token'])) {
    return header("location: /404.php");
}

$account = getAccount($pdo, $_SESSION['api_token']);

if ($account['grade'] < 800) {
    return header("location: /404.php");
}
$members = $pdo->query("SELECT * FROM users")->fetchAll();
$bansip = $pdo->query("SELECT * FROM banip")->fetchAll();
$allNews = $pdo->query("SELECT * FROM news")->fetchAll();
$result = [];
if (isset($_POST['write_news'])) {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = trim(htmlspecialchars($_POST['title']));
        $content = trim(htmlspecialchars($_POST['content']));
        if (!empty($title) && !empty($content)) {
            $pdo->prepare("INSERT INTO news (titre, contenu, auteur, date_post) VALUES (?,?,?,?)")->execute([$title, $content, $account['ID'], time()]);
            header("location: /panel.php");
        } else {
            $result['empty_filds'] = "Vous devez remplir tous les champs nécessaires à la création d'une news !";
        }
    } else {
        $result['empty_filds'] = "Vous devez remplir tous les champs nécessaires à la création d'une news !";
    }
}

if (isset($_POST['ban'])) {
    if (isset($_POST['member-id'])) {
        $memberID = trim(htmlspecialchars($_POST['member-id']));
        if (!empty($memberID)) {
            $memberAccount = getAccountWithID($pdo, $memberID);
            foreach (explode(",", $memberAccount['ip']) as $ip) {
                if (!empty($ip)) {
                    $pdo->prepare("INSERT INTO banip (ip) VALUES (?)")->execute([$ip]);
                }
            }
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$memberID]);
            $pdo->prepare("DELETE FROM news WHERE auteur=?")->execute([$memberID]);
            header("location: /panel.php");
        } else {
            $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
        }
    } else {
        $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
    }
}

if (isset($_POST['delete'])) {
    if (isset($_POST['member-id'])) {
        $memberID = trim(htmlspecialchars($_POST['member-id']));
        if (!empty($memberID)) {
            $memberAccount = getAccountWithID($pdo, $memberID);
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$memberID]);
            $pdo->prepare("DELETE FROM news WHERE auteur=?")->execute([$memberID]);
            header("location: /panel.php");
        } else {
            $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
        }
    } else {
        $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
    }
}

if (isset($_POST['verify'])) {
    if (isset($_POST['member-id'])) {
        $memberID = trim(htmlspecialchars($_POST['member-id']));
        if (!empty($memberID)) {
            $memberAccount = getAccountWithID($pdo, $memberID);
            updateAccount($pdo, null, true, "email_verified", $memberAccount['api_token']);
            header("location: /panel.php");
        } else {
            $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
        }
    } else {
        $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
    }
}

if (isset($_POST['delete-news'])) {
    if (isset($_POST['news-id'])) {
        $newsID = trim(htmlspecialchars($_POST['news-id']));
        if (!empty($newsID)) {
            $pdo->prepare("DELETE FROM news WHERE id=?")->execute([$newsID]);
            header("location: /panel.php");
        } else {
            $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
        }
    } else {
        $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
    }
}

if (isset($_POST['add-skin'])) {
    if (isset($_POST['skin-id'])) {
        $skin = getSkin($pdo, trim(htmlspecialchars($_POST['skin-id'])));
        if (!empty($skin)) {
            $skinID = trim(htmlspecialchars($_POST['skin-id']));
            if (isset($_POST['member-id'])) {
                $memberID = trim(htmlspecialchars($_POST['member-id']));
                if (!empty($memberID)) {
                    $memberAccount = getAccountWithID($pdo, $memberID);

                    if (!empty($memberAccount)) {
                        $newSkins = "";
                        foreach (explode(",", $memberAccount['skinsList']) as $skin) {
                            if ($skin !== "") {
                                $newSkins .= "$skin,";
                            } else {
                                $newSkins .= "$skinID,";
                            }
                        }
                        updateAccount($pdo, 0, $newSkins, "skinsList", $memberAccount['api_token']);
                        header("location: /panel.php");
                    } else {
                        $result['invalid_memberid'] = "L'identifitant du membre semble invalide !";
                    }
                } else {
                    $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
                }
            } else {
                echo "3";
                $result['empty_memberid'] = "Une erreur s'est produite, rafraichissez la page puis réessayez !";
            }
        } else {
            $result['invalid_skinid'] = "L'identifiant du skin ne semble pas valide !";
        }
    } else {
        $result['empty_skinid'] = "Une erreur s'est produite, merci de rentrer un identifiant de skin !";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Panel</title>
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

    <?php if (!empty($members)) { ?>
        <div class="container jumbotron">

            <h3 style="text-align: center;">Membres</h3>
            <hr>

            <table class="table">
                <thead>

                    <tr>
                        <th scope="col" class="col-sm">ID</th>
                        <th scope="col" class="col-sm">Login</th>
                        <th scope="col" class="col-sm">Pseudo</th>
                        <th scope="col" class="col-sm">BBL</th>
                        <th scope="col" class="col-sm">XP</th>
                        <th scope="col" class="col-sm">Genre</th>
                        <th scope="col" class="col-sm">Date d'inscription</th>
                        <th scope="col" class="col-sm">Skins</th>
                        <th scope="col" class="col-sm">Action</th>
                        <th scope="col" class="col-sm">Confirmation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member) {
                        $date = date("d/m/Y à H:i:s", $member['registerdate']);
                        $pseudo = "";
                        if (!empty($member['pseudo'])) {
                            $pseudo = $member['pseudo'];
                        } else {
                            $pseudo = "<i>Aucun pseudo de jeu</i>";
                        }
                        echo "<tr>
                    <td>{$member['ID']}</td>
                    <td><a href=\"/members.php?id={$member['ID']}\">{$member['login']}</a></td>
                    <td>{$pseudo}</td>
                    <td>{$member['bbl']}</td>
                    <td>{$member['xp']}</td>
                    <td>{$member['genre']}</td>
                    <td>{$date}</td>
                    <td><span style=\"word-break: break-all;\">{$member['skinsList']}</span><br><hr><form method=\"POST\">
                    <div>
                    <input class=\"form-control\" type=\"text\" name=\"skin-id\" placeholder=\"ID du skin\" required>
                    </div>
                    <br>
                    <div>
                    <button class=\"btn btn-success\" type=\"submit\" name=\"add-skin\">Ajouter un skin</button>
                    </div>
                    <input type=\"hidden\" name=\"member-id\" value=\"{$member['ID']}\">
                    </form></td>
                    <td><form method=\"POST\">
                    <div>
                    <button class=\"btn btn-danger\" type=\"submit\" name=\"ban\">Bannir</button>
                    </div>
                    <br>
                    <div>
                    <button class=\"btn btn-danger\" type=\"submit\" name=\"delete\">Supprimer</button>
                    </div>
                    <input type=\"hidden\" name=\"member-id\" value=\"{$member['ID']}\">
                    </form></td>";
                        if (!$member['email_verified']) {
                            echo "<td><form method=\"POST\">
                        <div>
                        <button class=\"btn btn-primary\" type=\"submit\" name=\"verify\">Confirmer</button>
                        </div>
                        <input type=\"hidden\" name=\"member-id\" value=\"{$member['ID']}\">
                        </form></td>";
                        } else {
                            echo "<td><p>Confirmé</p></td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php } ?>


    <div class="container jumbotron">
        <?php if (!empty($allNews)) { ?>
            <h3 style="text-align: center;">News</h3>
            <hr>

            <table class="table">
                <thead>

                    <tr>
                        <th scope="col" class="col-sm-1">ID</th>
                        <th scope="col" class="col-sm-3">Titre</th>
                        <th scope="col" class="col-sm-2">Auteur</th>
                        <th scope="col" class="col-sm-2">Posté le</th>
                        <th scope="col" class="col-sm-2">Lien</th>
                        <th scope="col" class="col-sm-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allNews as $news) {
                        $date = date("d/m/Y", strtotime($news['date_post']));
                        $account = getAccountWithID($pdo, $news['auteur']);
                        $author = $account['login'];
                        if (!empty($account['pseudo'])) {
                            $author .= " (" . $account['pseudo'] . ")";
                        }
                        echo "<tr>
                <td>{$news['id']}</td>
                <td>{$news['titre']}</td>
                <td><a href=\"/members.php?id={$account['ID']}\">{$author}</a></td>
                <td>{$date}</td>
                <td><a href=\"/news.php?id={$news['id']}\">/news?id={$news['id']}</a>
                <td><form method=\"POST\">
                    <div>
                        <button class=\"btn btn-danger\" type=\"submit\" name=\"delete-news\">Supprimer</button>
                        <input type=\"hidden\" name=\"news-id\" value=\"{$news['id']}\">
                    </div>
                </form></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <h3 style="text-align: center;">Écrire une news</h3>
        <hr>
        <form method="post">
            <div>
                <input class="form-control" type="text" name="title" placeholder="Titre de l'article" required value="<?php if (isset($_POST['title'])) {
                                                                                                                            echo $_POST['title'];
                                                                                                                        } ?>">
            </div><br>
            <div>
                <textarea name="content" style="width:100%; height: 10vh;" required placeholder="Contenu de la news"><?php if (isset($_POST['content'])) {
                                                                                                                            echo $_POST['content'];
                                                                                                                        } ?></textarea>
            </div><br>
            <button type="submit" name="write_news" class="btn btn-lg btn-success w-100"><i class="fa fa-floppy-o" aria-hidden="true"></i> ENREGISTRER</button>

        </form>
    </div>
    <?php include_once("footer.php"); ?>

</body>

</html>
