<?php
session_start();
include_once("../../backend/database.php");

$req = $pdo->prepare('UPDATE actualites SET normal = :normal');
$req->execute(array(
'normal' => $_POST['DAILYMSG']
));
$resultat = $req->rowCount();
if($resultat == 1) {
	echo "RESULT=1";
} else {
	echo "RESULT=0";
}
