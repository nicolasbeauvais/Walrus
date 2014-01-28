<?php
namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class HelloController extends WalrusFrontController
{

    public function helloHome ()
    {
        $this->register('test', 'Hello World!');
        $this->register('beta', array('coucou', 'la vie'));
        $this->register('gamma', $this);

        $this->setView('home');
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
