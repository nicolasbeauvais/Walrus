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
        if (!$_SERVER['argv'][1]) {
            self::help();
        }
        $method = $_SERVER['argv'][1];

        if (isset($_SERVER['argv'][2])) {
            $param = $_SERVER['argv'][2];
        }

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

        // create testing directory
        if (!file_exists($filer->filerPathJoin('www', 'testing'))) {
            $startCreateTesting = microtime(true);
            echo 'Create testing project directory... ';

            $filer->setCurrentElem('www');
            $filer->folderCreate('testing');

            $filer->setCurrentElem('');
            $filer->copy('', $filer->pathJoin('www', 'testing'), $_ENV['W']['deploy']['blacklist']);
        } else {
            $answer = self::prompt('A testing project as been detected, resume deploy', array('yes', 'no'));

            if ($answer == 'no') {
                $startCreateTesting = microtime(true);
                echo 'Create testing project directory... ';

                $filer->setCurrentElem($filer->pathJoin('www', 'testing'));
                $filer->emptyFolder();
                $filer->setCurrentElem('');
                $filer->copy('', $filer->pathJoin('www', 'testing'), $_ENV['W']['deploy']['blacklist']);
            }
        }

        // add configuration to testing
        if (file_exists($filer->filerPathJoin('www', 'testing', 'config'))) {
            $filer->setCurrentElem($filer->pathJoin('www', 'testing', 'config'));
            $filer->emptyFolder();
        } else {
            $filer->setCurrentElem($filer->pathJoin('www', 'testing'));
            $filer->folderCreate('config');
        }

        $conf = $_ENV['W'];
        $conf['environment'] = 'production';

        // change 'url' to 'base_url'
        $config = WalrusCompile::newConfiguration($conf);
        $filer->setCurrentElem('');

        copy(
            $filer->filerPathJoin('config', 'config.php'),
            $filer->filerPathJoin('www', 'testing', 'config', 'config.php')
        );

        $filer->setCurrentElem($filer->pathJoin('www', 'testing', 'config', 'config.php'));
        $filer->changeFileContent($config);
        $filer->setCurrentElem('');

        copy(
            $filer->filerPathJoin('config', 'env.php'),
            $filer->filerPathJoin('www', 'testing', 'config', 'env.php')
        );
        $filer->setCurrentElem($filer->pathJoin('config', 'compiled.php'));
        $filer->moveCurrent($filer->pathJoin('www', 'testing', 'config'));

        if (isset($startCreateTesting)) {
            $timeCreateTesting = round((microtime(true) - $startCreateTesting), 2) . 's';
            echo 'done (' . $timeCreateTesting . ')' . "\r\n";
        }

        echo 'Your project as been deployed to the testing folder.' . "\r\n";

        $answer = self::prompt('Deploy testing', array('yes', 'no'));

        if ($answer == 'yes') {
            $name = 'deploy-' . date('Y-m-d His');
            $filer->setCurrentElem('');
            $filer->folderCreate($name);

            $filer->copy(
                $filer->pathJoin('www', 'testing'),
                $filer->pathJoin($name)
            );

        }

        $filer->setCurrentElem($filer->pathJoin('www', 'testing'));
        $filer->emptyFolder();
        $filer->deleteCurrent();

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
