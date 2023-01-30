<?php
session_start();
include_once("../../backend/database.php");

$req = $pdo->prepare('UPDATE users SET pseudo = :pseudo WHERE id = :id');
$req->execute(array(
'pseudo' => $_GET['PSEUDO'],
'id' => $_GET['USERID']
));
?>