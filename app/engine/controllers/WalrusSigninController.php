<?php

/**
 * Walrus Framework
 */

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\devises\Signin;

class WalrusSigninController extends WalrusController
{
    public function getSignin()
    {
        $this->setView(Signin::$options['template']);
    }

    public function postSignin()
    {
        if (!empty($_POST))
        {
            $res = $this->model('WalrusUser')->signin();
            if(isset($res['bad_credentials']))
            {
                $this->register('errors', array('bad_credentials' => $res['bad_credentials']));
                if(!empty($_POST[Signin::$options['login']['field']]))
                {
                    $this->register('email', $_POST[Signin::$options['login']['field']]);
                }
            }
            else
            {
                $this->go('/');
            }
        }

        $this->setView(Signin::$options['template']);
    }

    public function logout()
    {
        unset($_SESSION['id']);
        unset($_SESSION['login']);
        unset($_SESSION['acl']);

        $this->go($this->generate('_home'));
    }
}