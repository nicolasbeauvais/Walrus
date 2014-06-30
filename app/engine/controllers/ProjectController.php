<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class ProjectController extends WalrusController
{

    public function index(){


        $this->setView('index');
    }

}
