<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['api_token'])) {
	header("location: /signin.php");
}

include_once("backend/database.php");
include_once("backend/api.php");

$usersReq = $pdo->query("SELECT * FROM users ORDER BY xp");
$users = $usersReq->fetchAll();
$users = array_reverse($users);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once("head.php"); ?>
    <title>Blablaland - Classement</title>
</head>

<body>
    <?php include_once("header.php"); ?>
    <div class="container jumbothon">
        <table class="table">
            <thead>
		<th>Position</th>
                <th>Login</th>
                <th>Pseudo</th>
                <th>XP</th>
                <th>Badges</th>
            </thead>
            <tbody>
                <?php
		$index = 0;
                foreach ($users as $user) {
			$index++;
                    $badgeToHTML = "<div class=\"row\">";
                    $userBadges = explode(",", $user['badges']);
                    array_pop($userBadges);
                    foreach ($userBadges as $badgeID) {
                        $badge =  getBadge($pdo, $badgeID);
                        $badgeToHTML .= "<img class=\"col-lg-auto\" style=\"margin-left: 20px;\" width=\"50\" height=\"50\" src=\"{$badge['img']}\">";
                    }
                    $badgeToHTML .= "</div>";

                    echo "<tr>
			<td>{$index}</td>
                    <td><a href=\"/members.php?id={$user['ID']}\" target=\"_BLANK\">{$user['login']}</a></td>
                    <td>{$user['pseudo']}</td>
                    <td>{$user['xp']}</td>
                    <td>{$badgeToHTML}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br><br><br>
    <?php include_once("footer.php"); ?>

</body>

</html>
