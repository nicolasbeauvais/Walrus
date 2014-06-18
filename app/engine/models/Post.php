<?php

/**
 * Walrus Framework
 * File maintained by: Thomas Bentkowski (Harper)
 * Created: 11:35 29/01/14
 */

namespace app\engine\models;

use R;

class Post
{

    public function addPost()
    {
        $post = R::dispense('posts');
        $post->id_user = $_SESSION['id'];
        $post->message = $_POST['message'];
        R::store($post);
    }

    public function getStats()
    {
        $postsNb = R::find(
            'posts',
            'id_user = :id',
            array(':id' => $_SESSION['id'])
        );

        return count($postsNb);
    }

    public function getPosts()
    {
        $sql = 'SELECT posts.*,
                       users.*,
                       posts.id as id
                  FROM posts
                  JOIN users as users
                 WHERE id_user = users.id
              ORDER BY posts.id DESC';

        $posts = R::getAll($sql);
        return $posts;
    }

    public function getLast($last_id)
    {

        $sql = 'SELECT posts.*,
                       users.*,
                       posts.id as id
                  FROM posts
                  JOIN users as users
                    ON posts.id_user = users.id
                 WHERE posts.id > ' . (int)$last_id . '
              ORDER BY posts.id DESC';
        return R::getAll($sql);
    }
}
