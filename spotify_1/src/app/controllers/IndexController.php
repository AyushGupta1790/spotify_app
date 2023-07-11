<?php

use Phalcon\Mvc\Controller;
use MyApp\component\Token;
use MyApp\Models\Playlists;
use MyApp\Models\Users;
use MyApp\component\GetData;

class IndexController extends Controller
{
    public function indexAction()
    {
        if (!$this->cookies->has('token')) {
            $token = new Token();
            $mytoken = $token->getTokenValue(
                'e84a71e83d3a4c95a1b58f7115895a30',
                'e326743aeff64b5992b3ef8270b22510'
            );
            $users = Users::findFirst($this->cookies->get('id'));
            $users->token = $mytoken;
            $users->save();
        }

        $this->response->redirect('signup');
    }
    public function viewAction()
    {
        $ch = curl_init();
        $get = new GetData();
        $tokenid = $get->getUserById($this->cookies->get('id'));
        $header = [
            "Authorization: Bearer " . $tokenid[0]['token'],
        ];
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/recommendations?seed_artists=4NHQUGzhtTLFvgF5SZesLK&seed_genres=classical%2Ccountry&seed_tracks=0c6xIDDpzE81m2q797ordA");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = json_decode(curl_exec($ch), true);
        $this->view->data = $data;
        $this->session->set('view', $data);
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = json_decode(curl_exec($ch), true);
        $this->view->name = $data['display_name'];
    }
    public function submitAction()
    {
        $search = $this->request->getPost('search');
        $type = array();
        if (isset($_POST['albums'])) {
            array_push($type, 'album');
        }
        if (isset($_POST['artists'])) {
            array_push($type, 'artist');
        }
        if (isset($_POST['playlists'])) {
            array_push($type, 'playlist');
        }
        if (isset($_POST['tracks'])) {
            array_push($type, 'track');
        }
        if (isset($_POST['shows'])) {
            array_push($type, 'show');
        }
        if (isset($_POST['episodes'])) {
            array_push($type, 'episode');
        }
        $data = $this->getData->getData($search, $type);
        $this->view->type = $type;
        $this->session->set('data', $data);
    }
    public function addAction()
    {
        $type = $this->request->get('type');
        $id = $this->request->get('spotifyid');
        $playlist = new Playlists();
        $data = array(
            'spotify_id' => $id,
            'type' => $type
        );
        $playlist->assign(
            $data,
            [
                'spotify_id',
                'type'
            ]
        );
        $playlist->save();
        $this->response->redirect('index/view');
    }
}
