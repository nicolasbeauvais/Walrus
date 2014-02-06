<?php
/**
 * Walrus Framework
 * File maintained by: Thomas Bentkowski (Harper)
 * Created: 11:35 29/01/14
 */

namespace engine\models;

use R;

class User
{
    public function signup()
    {
        $user = R::dispense('users');
        $user->name = $_POST['name'];
        $user->pseudo = $_POST['pseudo'];
        $user->password = hash("sha256", 'salt' . $_POST['password']);
        $user->acl = 'user';
        R::store($user);

        $this->fillSession($user);
        return true;
    }

    public function login()
    {
        $user = R::findOne(
            'users',
            ' pseudo = :pseudo AND password = :password',
            array(
                ':pseudo' => $_POST['pseudo'],
                ':password' => hash("sha256", 'salt' . $_POST['password']),
            )
        );

        if ($user) {
            $this->fillSession($user);
            return true;
        }

        return false;
    }

    public function getLasts()
    {
        $users = R::find(
            'users',
            ' LIMIT 5'
        );

        return $users;
    }

    public function fillSession($bean)
    {
        $_SESSION['id'] = (int)$bean->id;
        $_SESSION['name'] = (string)$bean->name;
        $_SESSION['pseudo'] = (string)$bean->pseudo;
        $_SESSION['acl'] = (string)$bean->acl;
    }

    public function getCurrentUser($id)
    {
        $user = R::findOne('users', $id);

        if ($user) {
            return $user['pseudo'];
        } else {
            return false;
        }
    }
}
