<?php
namespace app\engine\models;

use R;

class User
{
    public function signup()
    {
        $user = R::dispense('users');

        $pseudo_exist = $users = R::find(
            'users',
            ' pseudo = :pseudo',
            array(
                ':pseudo' => $_POST['pseudo']
            )
        );

        // VALIDATION
        if (!$pseudo_exist) {
            $user->pseudo = $_POST['pseudo'];
        }
        else
        {
            $errors['pseudo.taken'] = 'Pseudo already taken';
        }

        if($_POST['password'] != $_POST['cpassword'])
        {
            $errors['password.confirm'] = 'Confirm password fail';
        }

        if(!empty($errors))
            return $errors;
        //___________________________________

        $user->name = $_POST['name'];
        $user->password = hash("sha256", 'salt' . $_POST['password']);
        $user->acl = 'user';

        R::store($user);

        $this->fillSession($user);
        return true;
    }

    public function signin()
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

    public function fillSession($bean)
    {
        $_SESSION['user']['id'] = (int)$bean->id;
        $_SESSION['user']['name'] = (string)$bean->name;
        $_SESSION['user']['pseudo'] = (string)$bean->pseudo;
        $_SESSION['user']['acl'] = (string)$bean->acl;
    }
}