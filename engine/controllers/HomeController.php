<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class HomeController extends WalrusFrontController
{

    public function run()
    {
        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'login') {
                $this->model('user')->login();
            } elseif ($_POST['type'] === 'signup') {
                $this->model('user')->signup();
            }
        }

        if (isset($_SESSION['name'])) {
            $this->reroute('dashboard', 'run');
        }

        $this->skeleton('_skeleton_home');
    }
}
