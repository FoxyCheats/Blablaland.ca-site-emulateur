<?php

function skinBeforeAndNext($tableSkinIndex)
{
    $newLowSkinIndex = $tableSkinIndex - 1;
    $newHighSkinIndex = $tableSkinIndex + 1;
    echo "<div class=\"nav row\">";
    if ($newLowSkinIndex >= 0) {
        echo "<a class=\"col-sm btn btn-success\" style=\"margin: 0 10px;\" href=\"/shop.php?id_skin={$newLowSkinIndex}\">Précédent !</a>";
    }
    echo "<a class=\"col-sm btn btn-success\" style=\"margin: 0 10px;\" href=\"/shop.php?id_skin={$newHighSkinIndex}\">Suivant !</a></div>";
}
