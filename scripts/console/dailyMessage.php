<?php session_start();
echo "RESULT=1&SESSION={$_SESSION['session']}GETLIST={$_POST['GETLIST']}";
