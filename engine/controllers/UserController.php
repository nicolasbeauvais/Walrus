<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class UserController extends WalrusFrontController
{

    public function run()
    {
        $this->register('test', 'COUCOU', '', 'testAlias');
        $this->skeleton('_skeleton_user');
    }
}
