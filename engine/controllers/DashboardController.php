<?php

namespace engine\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

class DashboardController extends WalrusFrontController
{

    public function run()
    {
        if (isset($_POST['post'])) {
            $this->model('post')->addPost();
            die;
        }

        $this->skeleton('_skeleton_main');

        $stats = $this->model('post')->getStats();
        $posts = $this->model('post')->getPosts();

        $this->register('stats', $stats);
        $this->register('posts', $posts);
    }
}
