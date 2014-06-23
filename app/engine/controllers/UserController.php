<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class UserController extends WalrusController
{
    public function signin()
    {
        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'login') {

                if(!$this->model('user')->signin())
                {
                    $this->register('errors', array('credentials' => 'wrong login/password'));
                }
                else
                {
                    $this->go('/');
                }
            }
        }

        $this->setView('signin');
    }

    public function signup()
    {
        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'signup') {
                $res = $this->model('user')->signup();
                if (isset($res['errors'])) {
                    $this->register('errors', $res['errors']);
                }
                else
                {
                    $this->go('/');
                }
            }
        }

        $this->setView('signup');
    }
}