<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class HelloController extends WalrusFrontController
{

    public function helloHome ()
    {
        $this->register('test', 'global work !');

        $this->setView('home', 'coucou');
        $this->register('beta', 'template 2 specified variable work', 'home', 'coucou');

        $this->setView('home', 'alias');
        $this->register('beta', 'template 1 specified variable work', 'home', 'alias');

        $this->setView('test/test');
        $this->register('gamma', $this, 'test/test');
    }

    public function doHelloWorld ($id = false)
    {
        echo 'Hello World ! ' . $id;
    }

    public function doHelloWorldNew()
    {
        $this->skeleton('_skeleton_main');
        $this->register('beta', 'coucou', '', 'testAlias', '_skeleton_main');
    }
}
