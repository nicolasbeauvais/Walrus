<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class DashboardController extends WalrusController
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
        $currentUser = $this->model('user')->getCurrentUser($_SESSION['id']);

        $this->register('stats', $stats);
        $this->register('posts', $posts);
        $this->register('currentUser', $currentUser);
    }
}
