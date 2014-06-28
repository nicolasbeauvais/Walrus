<?php

/**
 * Walrus Framework
 */

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\devises\Signup;

class WalrusSignupController extends WalrusController
{
    public function getSignup()
    {
        $this->setView(Signup::$options['template']);
    }
}