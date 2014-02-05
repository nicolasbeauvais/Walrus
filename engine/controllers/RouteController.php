<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class RouteController extends WalrusFrontController
{

    public function test()
    {
        $this->setView('sidebar/home');
    }

    public function testRoute()
    {
        echo 'EPIC WIN';
    }
}
