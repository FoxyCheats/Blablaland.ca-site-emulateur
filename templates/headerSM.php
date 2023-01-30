<?php

    include 'back/database.php';

    $logged = isset($_SESSION['session']);

    $maintenance = false;

    $reqMaintenance = $dbh->query("SELECT * FROM maintenance");
    $resMaintenance = $reqMaintenance->fetch();

    if($resMaintenance['maintenance'] == 1) {
        $maintenance = true;
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>Blablaland.ca - <?php echo $pageTitle ?></title>
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <script src="js/bootstrap.js"></script>
    <script src="js/swfobject.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="js/jq_functions.js"></script>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
    <script src="https://kit.fontawesome.com/2aa9e13b6e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/wysibb/theme/default/wbbtheme.css" />
    <script src="/wysibb/jquery.wysibb.min.js"></script>

</head>

<body>

    <?php 
        if (isset($_SESSION['session'])) {
            if($_SESSION['grade'] >= 200) {
                ?>
                <div id="staff_menu">
                    <ul>
                        <li><a href="/console/" target="_BLANK"><i class="fas fa-user-circle" style="margin-right: 5px"></i>  Console</a></li>
                        <li><p>   |   </p></li>
                        <li><a href="/panel/" target="_BLANK"><i class="fas fa-user" style="margin-right: 5px"></i>  Panel</a></li>
                    </ul>
                </div>
            <?php
            }
        }
    ?>

    <div id="ban"></div>
    <nav id="header">
        <div class="container">
            <div> <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle
                        navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span
                        class="icon-bar"></span> </button>
                <ul class="">
                    <li class="active">
                        <a href="index.php"><i class="fas fa-archway" style="margin-right: 5px"></i> Accueil</a>
                    </li>

                    <li>
                        <a href="/forum/" target="_BLANK"><i class="fa fa-pencil-alt" style="margin-right: 5px"></i> Forum</a>
                    </li>
                                
                    
                    
                    <li id="jouer">
                        <a href="jouer.php"><i class="fas fa-gamepad" style="margin-right: 5px"></i> Jouer</a>
                    </li>
                    
                
                    

                    <li>
                            <a target="_BLANK" class="menu_ws"><i class="fa fa-users" style="margin-right: 5px"></i> Communauté</a>
                            <ul class="sub_menu">
                                

                                <div id="contentWs">
                                    <li>
                                        <a href="classement.php" target="_BLANK"><i class="fa fa-star" style="margin-right: 5px"></i> Classement</a>
                                    </li>

                                    <li>
                                        <a href="annuaire.php"><i class="fa fa-users" style="margin-right: 5px"></i> Annuaire</a>
                                    </li>
                                </div>
                            </ul>
                    </li>

                    <li>
                        <a href="equipe.php"><i class="fas fa-user-shield" style="margin-right: 5px"></i> Équipe</a>
                    </li>


                    
                    <?php
                        if(isset($_SESSION["session"])) {
                            ?>
                            <!--<li><div id="viewskin" style="background-color:#000000;display: inline-block;"></li>
                            <script>
                                swfobject.embedSWF("/swfs/viewskin.swf?CACHE_VERSION=467", 
                                "viewskin", 
                                "54", 
                                "54", 
                                "20", 
                                "swfs/expressInstall.swf", {
                                    ACTION:<?php echo $userColumns["skinaction"]; ?>,
                                    CACHE_VERSION: 467,
                                    SKINID:"<?php echo $userColumns["skinid"]; ?>",
                                    SKINCOLOR:"<?php foreach(explode(',', $userColumns["skincolors"]) as $color){ echo urlencode(chr($color + 1)); }?>",
                                    FONDID:"1",
                                    SHOWSKIN:"<?php echo $userColumns["show_skin"]; ?>",
                                    USECACHE:"1",
                                    HIDEBORDER:"1"
                                }, {
                                    wmode:"transparent"
                                }, {
                                    quality:"high",
                                    scale:"noscale",
                                    salign:"TL",
                                    name:"viewskin"});
                            </script>
                            <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $userColumns["pseudo"].' ('.$userColumns["login"].') '; ?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                <li>
                                <?php 
                                $bbl = nb_format_std($userColumns["bbl"]);
                                $xp = nb_format_std($userColumns["xp"]);
                                echo "<button type=\"button\" class=\"btn btn-sm\" style=\"background: #fc01ca; color: #fff; width: 120px; margin-left: 20px; margin-bottom: 5px; \" id=\"BBLINFOS_BBL\"><img src=\"imgs/picto_blabillons.png\"> $bbl BBL</button>";
                                echo "<button type=\"button\" class=\"btn btn-sm\" style=\"background: #2096f2; color: #fff; width: 120px; margin-left: 20px; margin-bottom: 5px;\" id=\"BBLINFOS_XP\"><img src=\"imgs/picto_xp.png\"> $xp XP</button>";
                                echo "<button type=\"button\" class=\"btn btn-sm\" style=\"background: #04b40a; color: #fff; width: 120px; margin-left: 20px; margin-bottom: -10px;\" id=\"BBLINFOS_AMIS\"><img src=\"imgs/picto_amis.png\"> 0/30 Amis</button>";
                                ?></li><hr>-->


                                    <li style="margin-left: 20px;"><a href="mon_compte.php"><i class="fa fa-cog" aria-hidden="true" style="margin-right: 5px"></i> Mon compte</a></li>
                                    <li><a href="disconnect.php"><i class="fa fa-sign-out" aria-hidden="true" style="margin-right: 5px"></i> Déconnexion</a></li>
                                </ul>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li><a href="connexion.php">Connexion</a></li>
                            <li><a href="inscription.php">M'INSCRIRE</a></li>
                            <?php
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="central">