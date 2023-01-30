<?php 
include('../../backend/database.php');

include('../../backend/usercontroller.php');

if(isset($_SESSION['session'])) {
    echo "BBL=".$userColumns["bbl"];
}
?>