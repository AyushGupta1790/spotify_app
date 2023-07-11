<?php

use MyApp\Models\Users;
use Phalcon\Mvc\Controller;
use MyApp\component\Token;


class LoginController extends Controller
{
    public function indexAction()
    {
        //redirect to view
    }
    public function loginAction()
    {
        $email = $this->request->getPost('email');
        $pswd = $this->request->getPost('pswd');
        $user = $this->db->fetchAll(
            "SELECT * FROM users where `email`='$email' and `pswd`='$pswd'",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        if (empty($user)) {
            echo "wrong credentials";
        } else {
            $this->cookies->set('id', $user[0]['id'], time() + 86400, "/");
            $this->cookies->send();
            if ($user[0]['token'] == 0) {
                $token = new Token();
                $mytoken = $token->getTokenValue(
                    'e84a71e83d3a4c95a1b58f7115895a30',
                    'e326743aeff64b5992b3ef8270b22510'
                );
                $users = Users::findFirst($user[0]['id']);
                $users->token = $mytoken;
                $users->save();
            }
            $clientid = 'e84a71e83d3a4c95a1b58f7115895a30';
            $this->response->redirect("https://accounts.spotify.com/authorize?response_type=code&client_id=$clientid&scope=user-read-private&redirect_uri=http://localhost:8080/index/view&code_challenge_method=S256");
        }
    }
}
