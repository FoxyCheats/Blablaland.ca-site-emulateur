<?php session_start();
echo "RESULT=1";
include_once("../../backend/database.php");
$req = $pdo->prepare('UPDATE users SET grade = :grade WHERE id = :id');
$req->execute(array(
    'grade' => $_GET['GRADE'],
    'id' => $_GET['USERID']
));
