<?php
session_start();
if(!isset($_SESSION['session'])) {
	echo "RESULT=1&SESSION=0";
} else {
	echo "RESULT=1&SESSION=" . $_SESSION['session'];
}
?>