<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class HelloController extends WalrusFrontController
{

    public function helloHome ()
    {
        $this->register('test', 'global work !');

        $this->setView('home');
        $this->register('beta', 'template 1 specified variable work', 'home');

        $this->setView('test/test');
        $this->register('gamma', $this, 'test/test');
    }

    public function doHelloWorld ($id = false)
    {
        echo 'Hello World ! ' . $id;
    }

    public function doHelloWorldNew()
    {
        echo 'new Hello World !';
    }
}
