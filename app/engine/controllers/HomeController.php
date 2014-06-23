<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class HomeController extends WalrusController
{
    public function index()
    {
        $results = $this->model('post')->index();
        if(!empty($results))
        {
            $this->register('posts', $results);
        }

        $this->setView('home/index');
    }
}