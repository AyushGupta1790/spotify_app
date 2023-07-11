<?php

namespace MyApp\component;

use Phalcon\Di\Injectable;

class Token extends Injectable
{
    function getTokenValue($clientid, $clintpassword)
    {
        $ch = curl_init();
        $header = [
            "Content-Type: application/x-www-form-urlencoded"
        ];
        $data = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientid,
            'client_secret' => $clintpassword
        ]);
        curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch));
        $this->cookies->set(
            'token',
            $result->access_token,
            time() + 3600,
            "/"
        );
        $this->cookies->send();
        return $result->access_token;
    }
}
