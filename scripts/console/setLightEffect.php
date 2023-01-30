<?php
session_start();
include_once("../../backend/usercontroller.php");
include_once("../../backend/database.php");

if (boolval($logged)) {
    $session = $_POST['SESSION'];
    $light = $_POST['LIGHT'];
    $color = $_POST['COLOR'];
    $ncolor = dechex($color);

    $req = $pdo->prepare("SELECT * FROM users WHERE session = ? LIMIT 1");
    $req->execute([$session]);
    $data = $req->fetch();
    $grade = $data['grade'];
    echo $grade;
    if ($grade >= 200) {
        $stmt = $pdo->prepare("UPDATE users SET briller=?, brillance=? WHERE session=?");
        $stmt->execute([$light, $ncolor, $session]);

        echo "COLOR=$color";
        echo "&NCOLOR=$ncolor";
        die("&RESULT=1");
    }
}
echo 'RESULT=0';
