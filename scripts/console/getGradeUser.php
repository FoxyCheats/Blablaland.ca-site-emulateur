<?php
session_start();
include_once('../../backend/database.php');
echo"RESULT=1&GRADE=";
$session = $_SESSION['session'];
$res = $pdo->query("SELECT * FROM users WHERE session = $session");
while($data = $res->fetch()) {
	echo $data['grade'];
}
?>