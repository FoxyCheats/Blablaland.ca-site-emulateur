<?php
function usingProxy($ip)
{
    if (!empty($_SERVER['GEOIP_COUNTRY_CODE']) && $_SERVER['GEOIP_COUNTRY_CODE'] == 'A1') {
        return true;
    } else if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && $_SERVER['HTTP_CF_IPCOUNTRY'] == 'T1') {
        return true;
    }
    if (isset($_SERVER['REMOTE_ADDR'])) {

        $ports = array(8080, 80, 81, 1080, 6588, 8000, 3128, 553, 554, 4480, 3144);
        foreach ($ports as $port) {
            if (@fsockopen($_SERVER['REMOTE_ADDR'], $port, $null, $null, 0.010)) {
                return true;
            }
        }
    }


    return false;
}

