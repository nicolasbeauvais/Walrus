<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:59 27/01/14
 */

namespace Walrus\core;

use Walrus\core\objects\Skeleton;
use Walrus\core\objects\Template;
use MtHaml;
use Smarty;
use Spyc\Spyc;
use Exception;

/**
 * Class WalrusFrontController
 * @package Walrus\core
 */
class WalrusFrontController
{
    /**
     * All requested templated as a stack.
     * @var array
     */
    private static $templates = array();

    /**
     * 'Global' variables for all templates.
     * @var array
     */
    private static $variables = array();

    /**
     * Contain skeleton if they have been requested.
     * @var string
     */
    private static $skeletons = array();

    /**
     * Contain the smarty object if needed
     * @var obj
     */
    private static $smarty;

    /**
     * Contain all data specific to the template engine.
     * @var array
     */
    private static $templating = array();

    // @TODO: refactor
    private static $foreach_value;
    private static $foreach_key;
    private static $foreach_skeleton_value;
    private static $foreach_skeleton_key;

    /**
     * Init templating variables.
     */
    public function __construct()
    {
        switch ($GLOBALS['WalrusConfig']['templating']) {
            case 'haml':
                self::$templating[0] = '.haml';
                self::$templating[1] = '.php';
                break;
            case 'smarty':
                self::$templating[0] = '.tpl';
                self::$templating[1] = '';
                break;
        }
    }

    /**
     * Add a template to the stack.
     *
     * @param $view
     * @throws \Exception
     */
    protected function setView($view)
    {
        if (strrpos($view, '/') === false) {
            $className = explode('\\', get_called_class());
            $controller = strtolower(str_replace('Controller', '', end($className)));
            $template = FRONT_PATH . $controller . '/' . $view . self::$templating[0];
        } else {
            $template = FRONT_PATH . $view . self::$templating[0];
        }

        $objTemplate = new Template();
        $objTemplate->setName($view);
        $objTemplate->setTemplate($template . self::$templating[1]);
        self::$templates[] = $objTemplate;
    }

    /**
     * Variables to be add in templates
     *
     * @param $key
     * @param $var
     * @throws \Exception
     */
    protected function register($key, $var)
    {
        if (!isset($key) || !isset($var)) {
            throw new Exception('[WalrusFrontController] missing argument for function register');
        }

        self::$variables[$key] = $var;
    }

    /**
     * Display all templates in the stack order.
     */
    public static function execute()
    {
        if ($GLOBALS['WalrusConfig']['templating'] == 'smarty') {
            self::$smarty = new Smarty();
        }

        if (count(self::$variables) > 0) {
            foreach (self::$variables as self::$foreach_key => self::$foreach_value) {
                ${self::$foreach_key} = self::$foreach_value;
                if ($GLOBALS['WalrusConfig']['templating'] == 'smarty') {
                    self::$smarty->assign(self::$foreach_key, self::$foreach_value);
                }
            }
        }

        if (count(self::$templates) > 0) {

            foreach (self::$templates as self::$foreach_key => self::$foreach_value) {
                if (is_a(self::$foreach_value, 'Walrus\core\objects\Skeleton')) {
                    self::process(self::$foreach_value);
                } else {
                    switch ($GLOBALS['WalrusConfig']['templating']) {
                        case 'haml':
                            self::compileToYaml(substr(self::$foreach_skeleton_value->getTemplate(), 0, -4));
                            require(self::$foreach_skeleton_value->getTemplate());
                            break;
                        case 'smarty':
                            self::$smarty->display(self::$foreach_skeleton_value->getTemplate());
                            break;
                    }
                }
            }
        }
    }

    /**
     * child process for execute
     */
    private static function process()
    {
        foreach (self::$foreach_value->getTemplates() as self::$foreach_skeleton_key => self::$foreach_skeleton_value) {

            switch ($GLOBALS['WalrusConfig']['templating']) {
                case 'haml':
                    self::compileToYaml(substr(self::$foreach_skeleton_value->getTemplate(), 0, -4));
                    require(self::$foreach_skeleton_value->getTemplate());
                    break;
                case 'smarty':
                    self::$smarty->display(self::$foreach_skeleton_value->getTemplate());
                    break;
            }
        }
    }


    /**
     * Set the use of a skeleton.
     */
    public function skeleton($name)
    {

        if (count(self::$skeletons) === 0) {
            $this->loadSkeletons();
        }

        foreach (self::$skeletons as $skeleton) {
            if ($skeleton->getName() === $name) {
                self::$templates[] = $skeleton;
                break;
            }
        }
    }

    /**
     * Load skeletons from skeleton.yml.
     */
    private function loadSkeletons()
    {
        $skeleton_yaml = "../config/skeleton.yml";

        if (!file_exists($skeleton_yaml)) {
            throw new Exception('[WalrusFrontController] skeleton.yml doesn\'t exist in config/ directory');
        }

        $skeletons = Spyc::YAMLLoad($skeleton_yaml);

        foreach ($skeletons as $skeletonName => $skeleton) {

            $templates = array();

            foreach ($skeleton as $name => $value) {

                $template = FRONT_PATH . $value['template'] . self::$templating[0];

                $objTemplate = new Template();
                $objTemplate->setName($name);
                $objTemplate->setTemplate($template . self::$templating[1]);
                $templates[] = $objTemplate;
            }

            $objSkeleton = new Skeleton();
            $objSkeleton->setName($skeletonName);
            $objSkeleton->setTemplate($templates);

            self::$skeletons[] = $objSkeleton;
        }
    }

    /**
     * Compile yaml file.
     */
    private static function compileToYaml ($template)
    {
        $haml = new MtHaml\Environment('php');

        if (!file_exists($template)) {
            throw new Exception('[WalrusFrontController] requested template does not exist: ' . $template);
        }

        // @TODO: use WalrusFileManager
        $hamlCode = file_get_contents($template);

        if (!file_exists($template . '.php') || filemtime($template . '.php') != filemtime($template)) {
            $phpCode = $haml->compileString($hamlCode, $template);
            $tempnam = tempnam(dirname($template), basename($template));
            file_put_contents($tempnam, $phpCode);
            rename($tempnam, $template.'.php');
            touch($template.'.php', filemtime($template));
        }
    }
}
