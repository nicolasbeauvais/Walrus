<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 19:25 09/04/14
 */

namespace Walrus\core;

/**
 * - config/
 *   config.yml
 *   cmp-config.php
 *   env.php
 *   - routes/
 *       routes-*.yml
 *       cmp-routes.php
 *   - skeleton/
 *       skeleton-*.yml
 *       cmp-skeleton.php
 *   - forms/
 *       forms-*.yml
 *       cmp-forms.php
 *   compiled.php <= master
 */
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
     * @return WalrusCompile
     */
    public static function launch()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
            self::construct();
            self::setup();
        }

        return self::$instance;
    }

    /**
     * Private construct to init class attributes
     */
    private static function construct()
    {
        self::$configPath = $_ENV['W']['ROOT_PATH'] . 'config';
        self::$compiledPath = self::$configPath . DIRECTORY_SEPARATOR . 'compiled';
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
     */
    private static function setup()
    {
        // check config exist
        if (file_exists($_ENV['W']['ROOT_PATH'] . 'config/config.php')) {
            require_once($_ENV['W']['ROOT_PATH'] . 'config/config.php');
        } else {
            $_ENV['W']['templating'] = 'php';
            WalrusRouter::reroute('config', 'config');
            return false;
        }

        if ($_ENV['W']['environment'] == 'development') { // if dev
                self::compile();
        } else {
            require_once($_ENV['W']['ROOT_PATH'] . 'config/config.php');
            require_once($_ENV['W']['ROOT_PATH'] . 'config/conpiled.php');
        }

        return true;
    }

    /**
     * Check and compile if necessary all config files, for development environment only
     */
    private static function compile()
    {
        clearstatcache();

        $filer = new WalrusFileManager(self::$configPath);
        $tree = $filer->getFolderTree();

        if (isset($tree['compiled'])) {
            unset($tree['compiled']);
        }

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

                    //touch($filer->getCurrentElem());
                }
            }

            $filer->setCurrentElem('');
        }

        self::load();
    }

    /**
     * Load all compiled files
     */
    private static function load()
    {
        $filer = new WalrusFileManager(self::$compiledPath);

        $tree = $filer->getElements(true);

        foreach ($tree as $dir => $entity) {

            if (empty($entity)) {
                continue;
            }

            foreach ($entity as $file) {
                $filer->setCurrentElem($filer->pathJoin($dir, $file));
                $content = $filer->getFileContent();
                $_ENV['W'][$dir] = unserialize($content);
            }
        }
    }

    /**
     * Compile all yaml files into one compiled.php files (used by CLI, for production environment only).
     */
    private static function compileForProduction()
    {
        foreach (self::$entities as $entitie) {
            //@TODO
        }
    }
}
