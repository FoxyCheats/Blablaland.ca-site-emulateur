<?php

class ReCaptcha
{
    private $secret_key;

    function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
    }

    public function checkCode($token, $ip)
    {
        if (empty($token)) {
            return false;
        }

        $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify?secret={$this->secret_key}&response={$token}&remoteip={$ip}";
        if (function_exists("curl_version")) {
            $curl = curl_init($recaptcha_url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        } else {
            $response = file_get_contents($recaptcha_url);
        }

        if (empty($response) || is_null($response)) {
            return false;
        }
        $jsonResponse = json_decode($response);
        return $jsonResponse->success;
    }
}
