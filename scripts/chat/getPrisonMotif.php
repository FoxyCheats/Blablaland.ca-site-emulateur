<?php 
include('../../backend/usercontroller.php');

if($logged) {
    $xp = $chatColumns['xp_expire'];
    $motif = $chatColumns['prison_motif'];

    echo "MOTIF=$motif&EXPIRE=$xp";
}
?>