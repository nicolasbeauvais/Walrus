<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 13:26 28/01/14
 */

namespace Walrus\core;

use Exception;

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
        if (strpbrk($name, "\\/?%*:|\"<>")) {
            echo $name . ' isn\'t a valid controller name' . "\n";
            return;
        }

        $name = ucwords(strtolower($name));
        $filer = new WalrusFileManager(ROOT_PATH);

        if (!file_exists(ROOT_PATH . 'engine/controllers/' . $name . 'Controller.php')) {

            // @TODO: catch isn't working ?
            try {
                $filer->setCurrentElem('Walrus/core/sample/controller.sample');
                $controller = $filer->getFileContent();
                $controller = str_replace('%name%', $name, $controller);

                $filer->setCurrentElem('engine/controllers');
                $filer->fileCreate($name . 'Controller.php');
                $filer->setCurrentElem('engine/controllers/' . $name . 'Controller.php');
                $filer->changeFileContent($controller);
                echo 'New controller created in engine/controllers with the name ' . $name . 'Controller.php' . "\n";
            } catch (Exception $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . 'Controller.php already exist' . "\n";
        }

        if (!file_exists(ROOT_PATH . 'www/templates/' . $name)) {

            try {

                $filer->setCurrentElem('www/templates');
                $filer->folderCreate(strtolower($name));

                echo 'New templates directory created in www/templates with the name ' . $name . "\n";
            } catch (Exception $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . ' template directory already exist' . "\n";
        }

    }
}
