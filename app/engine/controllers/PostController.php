<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;

class PostController extends WalrusController
{
    public function show($id)
    {
        $result = $this->model('post')->find($id);

        if(is_array($result))
        {
            $this->register('error', 'Post doesnt exist');
        }
        else
        {
            $this->register('session', $_SESSION);
            $this->register('post', $result);
        }

        $this->setView('post/show');
    }

    public function create()
    {
        if(!empty($_POST))
        {
            $res = $this->model('post')->create();
            if(!empty($res['title.empty']))
            {
                $this->register('errors', $res);
            }
            else
            {
                $this->go('/post/'.$res->getProperties()['id']);
            }
        }

        $this->setView('post/new');
    }

    public function edit($id)
    {
        if(!empty($_POST))
        {
            $res = $this->model('post')->edit($id);
            if(!empty($res['title.empty']))
            {
                $this->register('errors', $res);
            }
            else
            {
                $this->go('/post/'.$res->getProperties()['id']);
            }
        }

        $result = $this->model('post')->find($id);

        if(is_array($result))
        {
            $this->register('error', 'Post doesnt exist');
        }
        else

            $this->register('post', $result);
        }

        $this->setView('post/edit');

    }

    public function delete($id)
    {
        $result = $this->model('post')->delete($id);

        if(!empty($result))
        {
            $this->register('error', $result);
            $this->go('/post/'.$id);
        }

        $this->go('/');
    }
}