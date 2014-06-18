<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class SidebarController extends WalrusController
{

    public function home ()
    {
        $this->setView('home');

        $users = $this->model('user')->getLasts();
        $this->register('users', $users);
    }
}
