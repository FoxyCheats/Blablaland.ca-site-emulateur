<?php

include_once('database.php');

$id = trim(htmlspecialchars($_GET['id']));
if (!empty($id)) {
	if ($id == 1) {
		if (isset($_SESSION['ID'])) {

			if ($_SESSION['grade'] >= 200) {

				$changeSkin = $dbh->prepare("UPDATE users SET skinid = 80 WHERE ID = ?");
				$changeSkin->execute(array($_SESSION['ID']));
			} else {
				header('location: https://blablaland.ca');
			}
		} else {
			header('location: https://blablaland.ca');
		}
		header('location: https://blablaland.ca');
	} else if ($id == 2) {

		if (isset($_SESSION['ID'])) {

			if ($_SESSION['grade'] >= 200) {

				$changeSkin = $dbh->prepare("UPDATE users SET skinid = 5 WHERE ID = ?");
				$changeSkin->execute(array($_SESSION['ID']));
			} else {
				header('location: https://blablaland.ca');
			}
		} else {
			header('location: https://blablaland.ca');
		}
		header('location: https://blablaland.ca');
	}
} else {
	header('location: https://blablaland.ca');
}
