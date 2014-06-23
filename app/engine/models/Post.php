<?php
namespace app\engine\models;

use R;

class Post
{
    public function delete($id)
    {
        $post = R::load('posts', $id);

        if($post->getProperties()['id'] == 0)
        {
            return array('post.not_found' => 'Post doesnt exist');
        }

        if($_SESSION['user']['id'] != $post->getProperties()['users_id'])
        {
            return array('user.forbidden' => 'Vous n\'avez pas les droits de faire ca');
        }

        R::trash($post);
    }

    public function find($id)
    {
        $post = R::load('posts', $id);

        if($post->getProperties()['id'] == 0)
        {
            return array('post.not_found' => 'Post doesnt exist');
        }

        return $post;
    }

    public function index()
    {
        $posts = R::findAll('posts');

        return $posts;
    }

    public function create()
    {
        $post = R::dispense('posts');

        if(empty($_POST['title']))
        {
            return array('title.empty' => 'Title can\'t be empty');
        }

        $user = R::load(
            'users',
            $_SESSION['user']['id']
        );

        $post->title = $_POST['title'];
        $post->content = $_POST['content'];

        $user->ownPostList[] = $post;
        R::store($user);

        return $post;
    }

    public function edit($id)
    {
        $post = R::load('posts', $id);

        if(empty($_POST['title']))
        {
            return array('title.empty' => 'Title can\'t be empty');
        }

        $post->title = $_POST['title'];
        $post->content = $_POST['content'];

        R::store($post);
        return $post;
    }
}