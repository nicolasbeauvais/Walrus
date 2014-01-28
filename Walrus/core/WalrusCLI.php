<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 13:26 28/01/14
 */
 

namespace Walrus\core;


class WalrusCLI
{
    /**
     * Check argv to execute the right method.
     */
    public static function execute()
    {
        if (count($_SERVER['argv']) == 1) {
            self::help();
        } elseif (count($_SERVER['argv']) === 3) {
            $method = $_SERVER['argv'][1];
            $param = $_SERVER['argv'][2];

            switch ($method) {
                case 'createController':
                    self::createController($param);
                    break;
                default:
                    self::help();// @TODO: display an intelligent help ?
            }

        } else {
            // @TODO: display an intelligent help ?
            echo 'error';
            echo "\n";
        }
    }

    private static function help()
    {
        // @TODO: create man
        echo "man tusk";
        echo "\n";
    }

    private static function createController ($name)
    {

    }
}
