<?php
session_start();
include_once('../../backend/usercontroller.php');
include_once('../../backend/database.php');

if (boolval($logged)) {

    $session = $_POST['SESSION'];
    $req = $pdo->prepare("SELECT * FROM users WHERE session = ? LIMIT 1");
    $req->execute([$session]);
    if ($req !== false) {

        $data = $req->fetch();
        $grade = $data['grade'];
        $light = $data['briller'];
        $color = $data['brillance'];

        if ($grade >= 200) {
            die("RESULT=1&LIGHT=$light&COLOR=$color");
        }
    }
}
echo 'RESULT=0';
