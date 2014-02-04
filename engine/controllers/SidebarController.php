<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class SidebarController extends WalrusFrontController
{

    public function home ()
    {
        $this->setView('home');
    }
}
