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

    public function admin ()
    {
        $this->skeleton('_skeleton_main');
        //$this->controller('route')->test();
        $this->reroute('route', 'testRoute');
        $this->skeleton('_skeleton_main');
    }
}
