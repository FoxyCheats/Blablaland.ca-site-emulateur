<?php

if (!isset($_SESSION)) {
    session_start();
}

include_once("backend/api.php");
include_once("backend/database.php");
include_once("backend/noproxy.php");
if (usingProxy(getIp())) {
    header("location: /noproxy.php");
}


if (!isset($isBanned) || $isBanned === false) {
    $banned = false;
    $hashedIP = hash("sha512", getIp());
    $isBanned = $pdo->query("SELECT * FROM banip")->fetch();
    if (!empty($isBanned)) {
        foreach ($isBanned as $ip) {
            if ($ip === $hashedIP) {
                $banned = true;
                break;
            }
        }

        if ($banned === true) {
            header("location: /ban.php");
        }
    }
}

$logged = false;
if (isset($_SESSION) && !empty($_SESSION['api_token'])) {
    $logged = boolval(checkAPIToken($pdo, $_SESSION['api_token']));
}

$req = $pdo->query("SELECT * FROM maintenance WHERE id = 1");
$isMaintenance = $req->fetch();

if ($isMaintenance['maintenance'] === "1" && $logged) {
    $account = getAccount($pdo, $_SESSION['api_token']);

    if ($account['grade'] !== "1000") {
        header("location: /maintenance.php");
    }
} else if ($isMaintenance['maintenance'] === "1" && $_SERVER['REQUEST_URI'] !== "/signin.php") {
    header("location: /maintenance.php");
}

$levels = array(
    0 => "Blabla",
    50 => "Ancien Staff",
    200 => "Anim Tchat",
    300 => "Blabla'Anonyme",
    500 => "Modo Tchat",
    600 => "Super Anim",
    601 => "Super Modo",
    700 => "Graphiste",
    800 => "Responsable",
    801 => "Responsable",
    1000 => "Développeur",
    1001 => "Développeuse",
    1400 => "Blabla Habitué",
    1800 => "Blabla Danseur"
);

$countries = array(
    '' => 'Aucun',
    'dz' => 'Algérie',
    'de' => 'Allemagne',
    'be' => 'Belgique',
    'ca' => 'Canada',
    'eg' => 'Egypte',
    'es' => 'Espagne',
    'us' => 'Etats-Unis',
    'fr' => 'France',
    'it' => 'Italie',
    'ch' => 'Suisse'
);

?>
<div id="ban"></div>
<nav id="header">
    <div class="container">
        <ul>
            <li class="active"><a href="index.php"><i class="fas fa-archway" style="margin-right: 5px"></i> Accueil</a></li>
            <li id="jouer"><a href="play.php"><i class="fas fa-gamepad" style="margin-right: 5px"></i> Jouer</a></li>
            <li><a href="https://discord.gg/XHNEXSFkcC" target="_blank"><i class="fas fa-bullhorn" style="margin-right: 5px"></i> Discord</a></li>


            <?php
            if ($logged) {
                $account = getAccount($pdo, $_SESSION['api_token']);
                $bbl = $account["bbl"];
                $xp = $account["xp"];
                $friends = [];
                foreach (explode(",", $account["amis"]) as $friend) {
                    if ($friend !== NULL && !empty($friend)) {
                        array_push($friends, $friend);
                    }
                }

            ?>
                <li id="community-menu">
                    <a href="#"><i class="fas fa-users" style="margin-right: 5px"></i> Communauté <span class="caret"></span></a>
                    <ul id="community-menu-dp">
                        <li class="hoverable"><a href="peoples.php"><i class="fa fa-users"></i> Annuaire</a></li>
                        <li class="hoverable"><a href="events.php"><i class="fa fa-calendar"></i> Événements</a></li>
                        <li class="hoverable"><a href="scoreboard.php"><i class="fa fa-bar-chart"></i> Classement</a></li>
                        <li class="hoverable"><a href="staff.php"><i class="fas fa-user-shield" style="margin-right: 5px"></i> Équipe</a></li>
                        <li class="hoverable"><a href="#"><i class="fas fa-align-center" style="margin-right: 5px"></i> Forum</a></li>
                    </ul>
                </li>
                <li><a href="shop.php"><i class="fas fa-shopping-cart" style="margin-right: 5px"></i> Boutique</a></li>
                <?php

                if ($account['grade'] >= 800) {
                ?>
                    <li id="panel-menu">
                        <a href="#"><i class="fas fa-cogs" style="margin-right: 5px"></i> Panel <span class="caret"></span></a>
                        <ul id="panel-menu-dp">
                        <li class="hoverable"><a href="console/index.html"><i class="fa fa-id-card"></i> Console</a></li> 
                        <li class="hoverable"><a href="panel.php"><i class="fa fa-id-card"></i> Administration</a></li>
                         <li class="hoverable"><a href="loto.php"><i class="fa fa-gift"></i> Loto</a></li>
                        </ul>
                    </li>
                <?php
                }  else if ($account['grade'] >= 200) {
                    ?>
                    <li id="panel-menu">
                    <a href="#"><i class="fas fa-cogs" style="margin-right: 5px"></i> Panel <span class="caret"></span></a>
                    <ul id="panel-menu-dp">
                    <li class="hoverable"><a href="console/index.html"><i class="fa fa-id-card"></i> Console</a></li> 
                    </ul>
                    </li>
                    <?php
                    }
                ?>

                

                <li><a href="chest.php"><i class="fas fa-chess-rook"></i> Coffre</a></li>

                <li id="account-manage-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><b><?php echo $account["login"];
                                                                                                                                            if (!empty($account['pseudo'])) {
                                                                                                                                                echo " ({$account['pseudo']})";
                                                                                                                                            } ?></b> <span class="caret"></span></a>
                    <ul id="account-manage-menu-dp">
                        <li style="background: #fc01ca;">
                            <button style="background: #fc01ca; border: none;" id="BBLINFOS_BBL"><img src="frontend/img/picto_blabillons.png"> <?= $bbl; ?> BBL</button>
                        </li>
                        <li style="background: #2096f2;">
                            <button style="background: #2096f2; border: none;" id="BBLINFOS_XP"><img src="frontend/img/picto_xp.png"> <?= $xp; ?> XP</button>
                        </li>
                        <li style="background: #04b40a;">
                            <button style="background: #04b40a; border: none;" id="BBLINFOS_AMIS"><img src="frontend/img/picto_amis.png"> <?= count($friends); ?> Amis</button>
                        </li>
                        <li>
                            <hr class="drop-down-separator">
                        </li>
                        <li class="hoverable"><a href="account.php"><i class="fa fa-cog"></i> Mon compte</a></li>
                        <li class="hoverable"><a href="disconnect.php"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                    </ul>
                </li>
            <?php
            } else {
            ?>
                <li><a href="signin.php">Connexion</a></li>
                <li><a href="signup.php">Inscription</a></li>
            <?php
            }
            ?>
        </ul>
    </div>
</nav>
