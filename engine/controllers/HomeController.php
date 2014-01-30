<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class HomeController extends WalrusFrontController
{

    public function run()
    {
        $this->register('test', 'COUCOU', '', 'testAlias');
        $this->skeleton('_skeleton_main');
    }
}
