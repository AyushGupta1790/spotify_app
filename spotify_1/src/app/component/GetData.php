<?php

namespace MyApp\component;

use Phalcon\Di\Injectable;

class GetData extends Injectable
{
    public function getData($search, $type)
    {
        $ch = curl_init();
        $get = new GetData();
        $id = $get->getUserById($this->cookies->get('id'));
        $header = [
            "Authorization: Bearer " . $id[0]['token'],
        ];
        $search = str_replace(" ", "%20", $search);
        $type = implode("%2C", $type);
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/search/?q=$search&type=$type");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        return json_decode(curl_exec($ch), true);
    }
    public function getById($id, $type)
    {
        $ch = curl_init();
        $get = new GetData();
        $tokenid = $get->getUserById($this->cookies->get('id'));
        $header = [
            "Authorization: Bearer " . $tokenid[0]['token'],
        ];
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/$type/$id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        return json_decode(curl_exec($ch), true);
    }

 
    public function getUserById($id)
    {
        $user = $this->db->fetchAll(
            "SELECT * FROM users where `id`='$id'",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        return $user;
    }
}
