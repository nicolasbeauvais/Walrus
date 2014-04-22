<?php

namespace engine\controllers;

use Walrus\core\WalrusFileManager;
use Walrus\core\WalrusFrontController as WalrusFrontController;

/**
 * Class HelloController
 * @package engine\controllers
 */
class HelloController extends WalrusFrontController
{

    public function run()
    {
        echo 'Hello Walrus!';
    }
}
