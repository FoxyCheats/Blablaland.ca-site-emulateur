<?php 
session_start();
if (!isset($_SESSION['ID'])) {
    return header("location: /");
}
include_once('../../backend/usercontroller.php');
include_once('../../backend/api.php');
include_once('../../backend/database.php');

    $login = trim(htmlspecialchars($_POST['LOGIN']));
    $pass = $_POST['PASS'];
    $account = getAccountWithID($pdo,$_SESSION['ID']);
    	if(
    	password_verify($pass, $account['password'])
    	){
    	exit ('RESULT=1&SESSION='.$account['session']);
    	}
        else{
            echo 'RESULT=0';
        }
?>