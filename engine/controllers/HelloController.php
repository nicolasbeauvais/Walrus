<?php

namespace engine\controllers;

use Walrus\core\WalrusFileManager;
use Walrus\core\WalrusController as WalrusController;

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
