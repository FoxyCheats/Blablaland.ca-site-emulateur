<?php
session_start();
include_once('../../backend/database.php');
echo "RESULT=1&";
$res = $pdo->query('SELECT * FROM grades');
while($data = $res->fetch()) {
	$n = $data['id'] - 1;
	$str = "ID_" . $n . "=" . $data['id'] . "&XP_" . $n . "=" . $data['xp'] . "&GRADE=" . $data['grade'] . "&NAME=" . urlencode($data['name']) . "&";
	if($n == $data['id']) {
		echo substr($str, 0, -1);
	}
	echo $str;
}
?>