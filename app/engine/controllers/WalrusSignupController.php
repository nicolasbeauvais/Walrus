<?php

/**
 * Walrus Framework
 */

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\devises\Signup;
use Walrus\core\devises\Signin;

class WalrusSignupController extends WalrusController
{
    public function getSignup()
    {
        $this->setView(Signup::$options['template']);
    }

    public function postSignup()
    {
        if (!empty($_POST))
        {
            $res = $this->model('WalrusUser')->signup();

            if (isset($res['errors'])) {
                $this->register('errors', $res['errors']);
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

        $this->setView(Signup::$options['template']);
    }
}