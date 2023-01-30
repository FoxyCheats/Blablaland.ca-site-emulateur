<?php
session_start();
include_once('../../backend/database.php');
include_once('../../backend/usercontroller.php');

if (empty($_SESSION['ID'])) exit();

$target = explode('&', $_POST['PARAMS'])[1];
$target = str_replace("TARGETID=", "", $target);
$id = $_SESSION['ID'];
if ($target == $id) exit("RES=0&ERROR=SELF_INVIT");

$countReq = $pdo->query("SELECT * FROM amis WHERE (demandeur = $target AND recepteur = $id) OR (demandeur = $id AND recepteur = $target)");
$count = 0;
if ($countReq !== false) {
    $count = $countReq->rowCount();
}

if ($_POST['ACTION'] == 1) {
    // Sender part :
    sendReqServerFriend(1, $id, $target);

} else if ($_POST['ACTION'] ==  2) {
    // Receiver part :
    $pdo->prepare("INSERT INTO amis (demandeur, recepteur) VALUES (?,?)")->execute([$target, $id]);
    sendReqServerFriend(2, $id, $target);
}


function sendReqServerFriend($action, $demandeur, $recepteur)
{
    $conn = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($conn, '154.49.216.178', 12301);
    socket_write($conn, "amis,$action,$demandeur,$recepteur");
    socket_close($conn);
}
