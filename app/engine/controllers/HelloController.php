<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

/**
 * Class HelloController
 * @package engine\controllers
 */
class HelloController extends WalrusController
{

    public function run()
    {
        echo 'Hello Walrus!';
    }
}
