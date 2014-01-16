<?php
namespace engine\controllers;

class HelloController
{
    public function helloHome ()
    {
        echo 'Home';
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
