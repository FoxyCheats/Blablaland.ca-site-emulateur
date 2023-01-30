<?php
include_once('../../backend/usercontroller.php');

if ($logged) {
    $pseudo = $_POST['PSEUDO'];

    $sql = "SELECT * FROM users WHERE pseudo LIKE '$pseudo%' ";

    $NB = 0;

    echo 'RESULT=1';

    foreach ($pdo->query($sql) as $data) {
        $pseudo = $data['pseudo'];
        $uid = $data['ID'];
        $ip = "SELECT * FROM users WHERE ip";

        echo "&PSEUDO_$NB=$pseudo";
        echo "&UID_$NB=$uid";
        echo "&IP_$NB=$ip";

        $NB++;
    }

    die("&NB=$NB");
}
echo 'RESULT=0';
