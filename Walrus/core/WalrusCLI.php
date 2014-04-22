<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 13:26 28/01/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

/**
 * Class WalrusCLI
 * @package Walrus\core
 */
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
                case 'createAPIController':
                    self::createAPIController($param);
                    break;
                case 'createModel':
                    self::createModel($param);
                    break;
                default:
                    self::help();
            }

        } else {
            self::help();
        }
    }

    /**
     * Give explanation about tusk command.
     *
     * In case no command are recognized, the tusk man is displayed.
     */
    private static function help()
    {
        echo "\n\tTusk is the Walrus Command Line Interface (CLI).\n";
        echo "\tTusk has been made in order to simplify the creation of models, controllers and API.\n";
        echo "\tIt will generate a simple file in a Walrus way.\n\n";
        echo "\tIn order to create a model you just have to write :\n";
        echo "\t\t php tusk createModel {name}\n\n";
        echo "\tIn order to create a controller you just have to write :\n";
        echo "\t\t php tusk createController {name}\n\n";
        echo "\tIn order to create a API controller you just have to write :\n";
        echo "\t\t php tusk createAPIController {name}\n\n";
        echo "\tIt is simple as this. Just replace {name} by your real name and that's it !\n\n";
        echo "\n";
    }

    /**
     * Create a controller and a directory in template.
     *
     * @param $name the controller name
     */
    private static function createController ($name)
    {
        if (strpbrk($name, "\\/?%*:|\"<>")) {
            echo $name . ' isn\'t a valid controller name' . "\n";
            return;
        }

        $name = ucwords(strtolower($name));
        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        if (!file_exists($_ENV['W']['ROOT_PATH'] . 'engine/controllers/' . $name . 'Controller.php')) {

            try {
                $filer->setCurrentElem('Walrus/core/sample/controller.sample');
                $controller = $filer->getFileContent();
                $controller = str_replace('%name%', $name, $controller);

                $filer->setCurrentElem('engine/controllers');
                $filer->fileCreate($name . 'Controller.php');
                $filer->setCurrentElem('engine/controllers/' . $name . 'Controller.php');
                $filer->changeFileContent($controller);
                echo 'New controller created in engine/controllers with the name ' . $name . 'Controller.php' . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . 'Controller.php already exist' . "\n";
        }

        if (!file_exists($_ENV['W']['ROOT_PATH'] . 'templates/' . $name)) {

            try {

                $filer->setCurrentElem('templates');
                $filer->folderCreate(strtolower($name));

                echo 'New templates directory created in templates with the name ' . $name . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . ' template directory already exist' . "\n";
        }

    }

    /**
     * Create a API controller
     *
     * @param $name the controller name
     */
    private static function createAPIController($name)
    {
        if (strpbrk($name, "\\/?%*:|\"<>")) {
            echo $name . ' isn\'t a valid controller name' . "\n";
            return;
        }

        $name = ucwords(strtolower($name));
        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        if (!file_exists($_ENV['W']['ROOT_PATH'] . 'engine/api/' . $name . 'Controller.php')) {

            try {
                $filer->setCurrentElem('Walrus/core/sample/APIController.sample');
                $controller = $filer->getFileContent();
                $controller = str_replace('%name%', $name, $controller);

                $filer->setCurrentElem('engine/api');
                $filer->fileCreate($name . 'Controller.php');
                $filer->setCurrentElem('engine/api/' . $name . 'Controller.php');
                $filer->changeFileContent($controller);
                echo 'New controller created in api/controllers with the name ' . $name . 'Controller.php' . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . 'Controller.php already exist' . "\n";
        }
    }

    /**
     * Create a new Model with the input the user gave.
     */
    private static function createModel ($name)
    {
        if (strpbrk($name, "\\/?%*:|\"<>")) {
            echo $name . ' isn\'t a valid model name.' . "\n";
            return;
        }
        if (preg_match('/[A-Z]/', $name) === 0) {
            $name = ucfirst($name);
        }

        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        if (!file_exists($_ENV['W']['ROOT_PATH'] . 'engine/models/' . $name . '.php')) {
            try {
                $filer->setCurrentElem('Walrus/core/sample/model.sample');
                $model = $filer->getFileContent();
                $model = str_replace('%name%', $name, $model);

                $filer->setCurrentElem('engine/models');
                $filer->fileCreate($name . '.php');
                $filer->setCurrentElem('engine/models/' . $name . '.php');
                $filer->changeFileContent($model);
                echo 'New model created in engine/models with the name ' . $name . '.php' . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . '.php already exist' . "\n";
        }
    }
}
