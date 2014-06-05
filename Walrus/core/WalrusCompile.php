<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 19:25 09/04/14
 */

namespace Walrus\core;

use Spyc\Spyc;

/**
 * Class WalrusCompile
 * @package Walrus\core
 */
class WalrusCompile
{
    /*
     * Path to the config directory
     */
    private static $configPath = '';

    /*
     * Path to the compiled directory
     */
    private static $compiledPath = '';

    /*
     * Directories to compile
     */
    private static $entities = array(
        'route',
        'skeleton',
        'form'
    );

    private static $instance;

    /**
     * Private construct to prevent multiples instances
     */
    private function __construct()
    {
    }

    /**
     * Private clone to prevent multiples instances
     */
    private function __clone()
    {
    }

    /**
     * Main function to call to get an instance of WalrusCompile.
     *
     * @param bool $deploy
     *
     * @return WalrusCompile
     */
    public static function launch($deploy = false)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
            self::construct();

            if (!$deploy) {
                return self::setup();
            } else {
                $yaml = self::compileYAMLForProduction();
                $helpers = self::compileHelpersForProduction();
                return $yaml && $helpers;
            }
        }
    }

    /**
     * Private construct to init class attributes
     */
    private static function construct()
    {
        self::$compiledPath = $_ENV['W']['CONFIG_PATH'] . DIRECTORY_SEPARATOR . 'compiled';
    }

    /**
     * Verify the current status dev / prod.
     *
     * if prod
     *   load compiled php files
     * else dev
     *   if a YAML config file as been modified, compile it
     *   else use compiled files
     *
     * @return bool
     */
    private static function setup()
    {
        // check config exist
        if (file_exists($_ENV['W']['ROOT_PATH'] . 'config' . DIRECTORY_SEPARATOR . 'config.php')) {
            require_once($_ENV['W']['ROOT_PATH'] . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        } else {

            $_ENV['W']['templating'] = 'php';

            self::compileHelpers();
            self::compileYAML();
            WalrusRouter::reroute('config', 'config');

            return false;
        }

        if ($_ENV['W']['environment'] == 'development') { // if dev
            self::compileHelpers();
            self::compileYAML();
        } else {
            require_once($_ENV['W']['ROOT_PATH'] . 'config/config.php');
            require_once($_ENV['W']['ROOT_PATH'] . 'config/compiled.php');
        }

        return true;
    }

    /**
     * Read the helpers directory to make a helpers array
     */
    private static function compileHelpers()
    {
        $filer = new WalrusFileManager($_ENV['W']['HELPERS_PATH']);
        $elements = $filer->getElements();

        $_ENV['W']['HELPERS'] = array();

        foreach ($elements as $element) {
            $className = substr($element, 0, -4);

            $_ENV['W']['HELPERS'][$className] = array(
                'class' => $className
            );
        }
    }

    /**
     * Check and compile if necessary all config files, for development environment only
     *
     * @return array of existing YAML files
     */
    private static function compileYAML()
    {

        clearstatcache();

        $filer = new WalrusFileManager($_ENV['W']['CONFIG_PATH']);
        $tree = $filer->getFolderTree();

        $_ENV['W']['development']['nb_file_compiled'] = 0;

        if (isset($tree['compiled'])) {
            unset($tree['compiled']);
        }

        $completeTree = $tree;

        // create directories if they don't exist
        foreach (self::$entities as $entity) {

            $entityPlu = $entity . 's';

            if (!array_key_exists($entityPlu, $tree)) {
                $filer->folderCreate($entityPlu);
                continue;
            }

            // check each file
            $filer->setCurrentElem($entityPlu);
            $entityTree = $filer->getElements();

            $completeTree[$entityPlu] = $entityTree;

            foreach ($entityTree as $YAMLFile) {

                $filer->setCurrentElem($entityPlu);

                // if yaml file
                if (is_int(strpos($YAMLFile, '.yml'))) {

                    $PHPFile = substr($YAMLFile, 0, -4) . '.php';

                    if (file_exists($filer->pathJoin(self::$compiledPath, $entityPlu, $PHPFile))) {
                        $datePHP = filemtime($filer->pathJoin(self::$compiledPath, $entityPlu, $PHPFile));
                        $dateYAML = filemtime($filer->filerPathJoin($YAMLFile));

                        if ($datePHP > $dateYAML) {
                            continue;
                        }
                    }

                    // check compiled directory
                    $filer->setCurrentElem('');
                    if (!file_exists($filer->filerPathJoin('compiled'))) {
                        $filer->folderCreate('compiled');
                    }

                    $filer->setCurrentElem('compiled');

                    if (!file_exists($filer->filerPathJoin($entityPlu))) {
                        $filer->folderCreate($entityPlu);
                    }

                    // compile
                    $filer->setCurrentElem($filer->pathJoin($entityPlu));
                    $content = Spyc::YAMLLoad($filer->filerPathJoin($YAMLFile));

                    $filer->setCurrentElem($filer->pathJoin('compiled', $entityPlu));

                    $compiled = serialize($content);

                    if (file_exists($filer->filerPathJoin($PHPFile))) {
                        $filer->setCurrentElem($filer->pathJoin('compiled', $entityPlu, $PHPFile));
                        $filer->changeFileContent($compiled);
                    } else {
                        $filer->fileCreate($PHPFile);
                        $filer->addFileContent($compiled);
                        $filer->setCurrentElem($filer->pathJoin('compiled', $entityPlu, $PHPFile));
                    }

                    $_ENV['W']['development']['nb_file_compiled']++;

                    touch($filer->getCurrentElem());
                }
            }

            $filer->setCurrentElem('');
        }

        self::load($completeTree);
    }

    /**
     * Load all compiled files
     *
     * @param array $YAMLTree a tree of th existing yaml conf files
     */
    private static function load($YAMLTree)
    {
        $filer = new WalrusFileManager(self::$compiledPath);

        $tree = $filer->getElements(true);

        foreach ($tree as $dir => $entity) {

            if (empty($entity)) {
                continue;
            }

            foreach ($entity as $file) {

                $fileName = substr($file, 0, -4);

                $filer->setCurrentElem($filer->pathJoin($dir, $file));

                if (!in_array($fileName . '.yml', $YAMLTree[$dir])) {
                    $filer->deleteCurrent();
                } else {
                    $content = $filer->getFileContent();
                    if (isset($_ENV['W'][$dir]) && !empty($_ENV['W'][$dir])) {
                        $_ENV['W'][$dir] = array_merge($_ENV['W'][$dir], unserialize($content));
                    } else {
                        $_ENV['W'][$dir] = unserialize($content);
                    }
                }
            }
        }
    }

    /**
     * Compile all php files into one compiled.php files (used by CLI, for production environment only).
     */
    private static function compileYAMLForProduction()
    {
        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        $filer->setCurrentElem($filer->pathJoin('Walrus', 'core', 'sample', 'compiled.sample'));
        $sample = $filer->getFileContent();

        $filer->setCurrentElem($filer->pathJoin('config', 'compiled'));
        $tree = $filer->getElements(true);



        $compiled = '';

        foreach ($tree as $dir => $entity) {

            if (empty($entity)) {
                continue;
            }

            $compiled .= "\r\n" . '// ' . $dir . "\r\n";

            foreach ($entity as $file) {
                $filer->setCurrentElem($filer->pathJoin('config', 'compiled', $dir, $file));
                $content = $filer->getFileContent();
                $phpArray = unserialize($content);

                $index = '$_ENV[\'W\'][\'' . $dir . '\']';
                $vars = self::convertPHPArrayToPHPString($index, $phpArray);

                $compiled .= $vars;
            }
        }

        $filer->setCurrentElem('');

        if (!file_exists($filer->filerPathJoin('config', 'compiled.php'))) {
            $filer->setCurrentElem('config');
            $filer->fileCreate('compiled.php');
        }

        $sample = str_replace('%date%', date('Y/m/d H:i:s'), $sample);
        $sample = str_replace('%content%', $compiled, $sample);

        $filer->setCurrentElem($filer->pathJoin('config', 'compiled.php'));
        $filer->changeFileContent($sample);

        return true;
    }

    /**
     * Compile helpers directory tree in the compiled.php files (used by CLI, for production environment only).
     */
    private static function compileHelpersForProduction()
    {
        self::compileHelpers();

        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        $name = 'HELPERS';
        $compiled = "\r\n" . '// ' . ucfirst(strtolower($name)) . "\r\n";

        $index = '$_ENV[\'W\'][\'' . $name . '\']';
        $compiled .= self::convertPHPArrayToPHPString($index, $_ENV['W']['HELPERS']);

        $filer->setCurrentElem($filer->pathJoin('config', 'compiled.php'));
        $filer->addFileContent($compiled);

        return true;
    }

    /**
     * Convert a PHP array to a valid PHP file
     *
     * @param string $index the output index
     * @param array $array a valid PHP array
     *
     * @return string
     */
    public static function convertPHPArrayToPHPString($index, $array)
    {
        $phpString = '';

        foreach ($array as $key => $row) {

            $itemIndex = $index . '[\'' . $key . '\']';

            if (is_array($row)) {
                $phpString .= self::convertPHPArrayToPHPString($itemIndex, $row);
            } elseif (is_string($row)) {
                $phpString .= $itemIndex . ' = "' . $row . '";' . "\r\n";
            } elseif (is_object($row)) {
                $phpString .= $itemIndex . ' = "' . serialize($row) . '";' . "\r\n";
            }
        }

        return $phpString;
    }

    /**
     * Create a new configuration
     *
     * @param $data an array of data to fill the new configuration.
     *
     * @return string the content of the new configuration file
     */
    public static function newConfiguration($data)
    {
        $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

        $filer->setCurrentElem('Walrus/core/sample/config.sample');
        $config = $filer->getFileContent();

        $config = str_replace('%rdbms%', strtolower($data['RDBMS']), $config);
        $config = str_replace('%host%', addslashes($data['host']), $config);
        $config = str_replace('%database%', $data['database'], $config);
        $config = str_replace('%name%', $data['name'], $config);
        $config = str_replace('%password%', $data['password'], $config);
        $config = str_replace('%url%', $data['base_url'], $config);
        $config = str_replace('%templating%', strtolower($data['templating']), $config);
        $config = str_replace('%environment%', strtolower($data['environment']), $config);

        return $config;
    }
}
