<?php

use Phalcon\Mvc\Controller;
use MyApp\Models\Users;

class SignupController extends Controller
{
    public function indexAction()
    {
        //redirect to view
    }

    public function registerAction()
    {
        $user = new Users();
        $data = array(
            'name' => $this->escaper->escapeHtml($this->request->getPost('name')),
            'email' => $this->escaper->escapeHtml($this->request->getPost('email')),
            'pswd' => $this->escaper->escapeHtml($this->request->getPost('pswd')),
            'pincode' => $this->escaper->escapeHtml($this->request->getPost('pincode')),

        );
        $user->assign(
            $data,
            [
                'name',
                'email',
                'pswd',
                'pincode',
            ]
        );
        $success = $user->save();
        if ($success) {
            $this->session->set('email', $data['email']);
            $this->session->set('pswd', $data['pswd']);
            $this->response->redirect('login');
        } else {
            $this->response->redirect('signup');
        }
    }
}
