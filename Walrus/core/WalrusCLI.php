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
        if (!isset($_SERVER['argv'][1])) {
            self::help();
        }
        $method = $_SERVER['argv'][1];

        if (isset($_SERVER['argv'][2])) {
            $param = $_SERVER['argv'][2];
        }

        switch ($method) {
            case 'createController':
                if (!isset($param)) {
                    self::help();
                }
                self::createController($param);
                break;
            case 'createAPIController':
                if (!isset($param)) {
                    self::help();
                }
                self::createAPIController($param);
                break;
            case 'createModel':
                if (!isset($param)) {
                    self::help();
                }
                self::createModel($param);
                break;
            case 'deploy':
                self::deploy();
                break;
            default:
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
        echo "\tTusk has been made to simplify the creation of models, controllers and API.\n";
        echo "\tIt will generate a simple file in a Walrus way.\n\n";
        echo "\tTo create a model:\n";
        echo "\t\t php tusk createModel {name}\n\n";
        echo "\tTo create a controller:\n";
        echo "\t\t php tusk createController {name}\n\n";
        echo "\tTo create a API controller:\n";
        echo "\t\t php tusk createAPIController {name}\n\n";
        echo "\tTo launch a deploy:\n";
        echo "\t\t php tusk deploy\n\n";
        echo "\tIt is simple as this. Just replace {name} by your file/class name and that's it !\n\n";
        echo "\n";
        die;
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
        $engine = $filer->pathJoin('app', 'engine', 'controllers');

        if (!file_exists(
            $_ENV['W']['ROOT_PATH'] . $filer->pathJoin($engine, $name . 'Controller.php')
        )) {

            try {
                $filer->setCurrentElem($filer->pathJoin('Walrus', 'core', 'sample', 'controller.sample'));
                $controller = $filer->getFileContent();
                $controller = str_replace('%name%', $name, $controller);

                $filer->setCurrentElem($engine);
                $filer->fileCreate($name . 'Controller.php');
                $filer->setCurrentElem($filer->pathJoin($engine, $name . 'Controller.php'));
                $filer->changeFileContent($controller);

                echo 'New controller created in ' . $engine . ' with the name ' . $name . 'Controller.php' . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . 'Controller.php already exist' . "\n";
        }

        $template = $filer->pathJoin('app', 'templates');
        if (!file_exists($_ENV['W']['ROOT_PATH'] . $filer->pathJoin('app', 'templates', $name))) {

            try {

                $filer->setCurrentElem($template);
                $filer->folderCreate(strtolower($name), 0777);

                echo 'New templates directory created in ' . $template . ' with the name ' . $name . "\n";
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
        $engine = $filer->pathJoin('app', 'engine', 'api');

        if (!file_exists($_ENV['W']['ROOT_PATH'] . $engine . $name . 'Controller.php')) {

            try {
                $filer->setCurrentElem($filer->pathJoin('Walrus', 'core', 'sample', 'APIController.sample'));
                $controller = $filer->getFileContent();
                $controller = str_replace('%name%', $name, $controller);

                $filer->setCurrentElem($engine);
                $filer->fileCreate($name . 'Controller.php');
                $filer->setCurrentElem($filer->pathJoin($engine, $name . 'Controller.php'));
                $filer->changeFileContent($controller);
                echo 'New controller created in ' . $engine . ' with the name ' . $name . 'Controller.php' . "\n";
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
        $engine = $filer->pathJoin('app', 'engine', 'models');

        if (!file_exists($_ENV['W']['ROOT_PATH'] . $engine . $name . '.php')) {
            try {
                $filer->setCurrentElem($filer->pathJoin('Walrus', 'core', 'sample', 'model.sample'));
                $model = $filer->getFileContent();
                $model = str_replace('%name%', $name, $model);

                $filer->setCurrentElem($engine);
                $filer->fileCreate($name . '.php');
                $filer->setCurrentElem($filer->pathJoin($engine, $name . '.php'));
                $filer->changeFileContent($model);
                echo 'New model created in ' . $engine . ' with the name ' . $name . '.php' . "\n";
            } catch (WalrusException $e) {
                echo 'Exception: ' . $e->getMessage() . "\n";
                return;
            }
        } else {
            echo $name . '.php already exist' . "\n";
        }
    }

    /**
     * Launch a deploy
     */
    private static function deploy()
    {
        include($_ENV['W']['ROOT_PATH'] . 'config' . DIRECTORY_SEPARATOR . 'deploy.php');

        $startDeploy = microtime(true);

        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        echo 'Launch Walrus deploy.' . "\r\n";

        // compile config
        $startCompile = microtime(true);
        echo 'Start compiling conf files...  ';
        WalrusCompile::launch(true);
        $timeCompile = round((microtime(true) - $startCompile), 2) . 's';
        echo 'done (' . $timeCompile . ')' . "\r\n";

        $startCreateProduction = microtime(true);
        echo 'Create production project directory... ';

        $name = 'deploy-' . date('Y-m-d His');
        $filer->setCurrentElem('');
        $filer->folderCreate($name);

        // copy project to deploy folder
        $_ENV['W']['deploy']['blacklist'][] = $name;
        $filer->copy('', $filer->pathJoin($name), $_ENV['W']['deploy']['blacklist']);

        // add configuration
        $filer->setCurrentElem($name);
        $filer->folderCreate('config');

        $conf = $_ENV['W'];
        $conf['environment'] = 'production';

        // set configuration
        $config = WalrusCompile::newConfiguration($conf);
        $filer->setCurrentElem('');

        copy(
            $filer->filerPathJoin('config', 'config.php'),
            $filer->filerPathJoin($name, 'config', 'config.php')
        );

        $filer->setCurrentElem($filer->pathJoin($name, 'config', 'config.php'));
        $filer->changeFileContent($config);
        $filer->setCurrentElem('');

        copy(
            $filer->filerPathJoin('config', 'env.php'),
            $filer->filerPathJoin($name, 'config', 'env.php')
        );
        $filer->setCurrentElem($filer->pathJoin('config', 'compiled.php'));
        $filer->moveCurrent($filer->pathJoin($name, 'config'));

        if (isset($startCreateProduction)) {
            $timeCreateTesting = round((microtime(true) - $startCreateProduction), 2) . 's';
            echo 'done (' . $timeCreateTesting . ')' . "\r\n";
        }

        $timeDeploy = round((microtime(true) - $startDeploy), 2) . 's';
        echo 'Deploy as been successful (' . $timeDeploy . ')' . "\r\n";
    }

    /**
     * Prompt something to user.
     *
     * @param string $ask a string displayed to the user when he fails
     * @param array $answers
     *
     * @return string
     */
    public static function prompt ($ask, $answers)
    {
        echo $ask . ' (' . join('/', $answers) . ') ? ';

        $handle = fopen("php://stdin", "r");

        $line = strtolower(trim(fgets($handle)));

        if (in_array($line, $answers)) {
            return $line;
        }

        self::prompt($ask, $answers);
    }
}
