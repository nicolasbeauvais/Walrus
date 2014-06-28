<?php
namespace app\engine\models;

use R;
use Walrus\core\devises\Signup;
use Walrus\core\devises\Signin;
use Walrus\core\WalrusI18n;

class WalrusUser
{
    public function signup()
    {
        $table = Signup::$options['table'];
        $login_field = Signin::$options['login']['field'];
        $password_field = Signup::$options['password']['field'];
        $cpassword_field = Signup::$options['password']['confirm']['field'];

        $user = R::dispense($table);

        $pseudo_exist = R::find(
            $table,
            $login_field.' = :login',
            array(
                ':login' => $_POST[$login_field]
            )
        );

        // VALIDATION
        if(empty($_POST[$login_field]))
        {
            $errors['errors']['login']['not_empty'] = WalrusI18n::get(Signup::$options['login']['not_empty']['message'], array('attribute' => ucwords($login_field)));
        }
        elseif($pseudo_exist)
        {
            $errors['errors']['login']['not_uniq'] = WalrusI18n::get(Signup::$options['login']['not_uniq']['message'], array('attribute' => ucwords($login_field)));
        }

        if(!preg_match(Signup::$options['password']['regex']['pattern'], $_POST[$password_field]))
        {
            $errors['errors']['password']['regex'] = WalrusI18n::get(Signup::$options['password']['regex']['message']);
        }
        elseif($_POST[$password_field] != $_POST[$cpassword_field])
        {
            $errors['errors']['password']['confirm'] = WalrusI18n::get(Signup::$options['password']['confirm']['message']);
        }

        if(!empty($errors))
            return $errors;
        //___________________________________

        $user->$login_field = $_POST[$login_field];
        $user->$password_field = hash(Signup::$options['password']['hash'], Signup::$options['password']['salt'] . $_POST[$password_field]);
        $user->acl = Signup::$options['default']['acl'];

        foreach(Signup::$options['additional_fields'] as $field)
        {
            $user->$field = $_POST[$field];
        }

        R::store($user);

        $this->fillSession($user);
        return true;
    }

    public function signin()
    {
        $table = Signup::$options['table'];
        $login_field = Signin::$options['login']['field'];
        $password_field = Signup::$options['password']['field'];

        $user = R::findOne(
            $table,
            $login_field.' = :login AND '.$password_field.' = :password',
            array(
                ':login' => $_POST[$login_field],
                ':password' => hash(Signup::$options['password']['hash'], Signup::$options['password']['salt'] . $_POST[$password_field]),
            )
        );

        if ($user) {
            $this->fillSession($user);
            return true;
        }
        else
        {
            $errors['bad_credentials'] = WalrusI18n::get(Signin::$options['bad_credentials']['message']);
        }

        return $errors;
    }

    public function fillSession($bean)
    {
        $login = Signup::$options['login']['field'];

        $_SESSION['id'] = (int)$bean->id;
        $_SESSION['login'] = (string)$bean->$login;
        $_SESSION['acl'] = (string)$bean->acl;
    }
}