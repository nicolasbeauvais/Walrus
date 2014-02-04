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

    /**
     * Safe foreach value handler
     * @var mixed
     */
    private static $foreach_value;

    /**
     * Safe foreach key handler
     * @var mixed
     */
    private static $foreach_key;


    /**
     * Array of instancied controllers
     */
    private $controllers;

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
     * Add a template to the template stack with ACL or not,
     * the stack is displayed by WalrusFrontController::execute()
     * at the end of Walrus execution.
     *
     * @param $view the template to add on templates stack
     * @param $acl
     *
     * @throws Exception
     */
    protected function setView($view, $acl = false)
    {
        if (strrpos($view, '/') === false) {
            $className = explode('\\', get_called_class());
            $controller = strtolower(str_replace('Controller', '', end($className)));
            $template = FRONT_PATH . $controller . '/' . $view . self::$templating[0];
        } else {
            $template = FRONT_PATH . $view . self::$templating[0];
        }

        if ($acl && (!isset($_SESSION['acl']) || $acl != $_SESSION['acl'])) {
            return;
        }

        $objTemplate = new Template();
        $objTemplate->setName($view);
        $objTemplate->setTemplate($template . self::$templating[1]);
        self::$templates[] = $objTemplate;
    }

    /**
     * Add a variable for front.
     *
     * All variables are given to all template (HAML|Smarty).
     *
     * @param $key
     * @param $var
     *
     * @throws Exception
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
     *
     * Execute is called by WalrusKernel as the last process of Walrus.
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
                switch ($GLOBALS['WalrusConfig']['templating']) {
                    case 'haml':
                        self::compileToYaml(substr(self::$foreach_value->getTemplate(), 0, -4));
                        require(self::$foreach_value->getTemplate());
                        break;
                    case 'smarty':
                        self::$smarty->display(self::$foreach_value->getTemplate());
                        break;
                }
            }
        }
    }

    /**
     * Add each template off a skeleton to the template stack.
     *
     * Skeleton's template are pushed to the template stack in the order they're
     * written in the skeleton.yml file.
     *
     * @param string $name the key of a skeleton defined in config/skeleton.yml
     */
    public function skeleton($name)
    {

        if (count(self::$skeletons) === 0) {
            $this->loadSkeletons();
        }

        foreach (self::$skeletons as $skeleton) {
            if ($skeleton->getName() === $name) {
                foreach ($skeleton->getTemplates() as $template) {
                    self::$templates[] = $template;
                }
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

                if (isset($value['acl']) && (!isset($_SESSION['acl']) || $value['acl'] != $_SESSION['acl'])) {
                    continue;
                }

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

    /**
     * Return an instance of the specified controller
     *
     * @param string $controller
     *
     * @return Class the specofied controller class
     * @throws Exception if the controller doesn't exist
     */
    protected function controller($controller)
    {
        $controllerClass = ucwords(strtolower($controller)) . 'Controller';

        if (isset($this->controllers[$controllerClass])) {
            return $this->controllers[$controllerClass];
        }

        $controllerClassWithNamespace =  WalrusAutoload::getNamespace($controllerClass);

        if (!$controllerClassWithNamespace) {
            throw new Exception('[WalrusFrontController] request unexistant controller: ' . $controllerClass);
        }

        $controllerInstance = new $controllerClassWithNamespace();
        $this->controllers[$controllerClass] = $controllerInstance;

        return $controllerInstance;
    }

    /**
     * Redirect the current route to the specified url.
     *
     * @param string $url
     */
    protected function go($url)
    {
        header('Location: ' . $url);
        die();
    }

    /**
     * Get the result of a route in a soft or hard way.
     *
     * @param string $url.
     * @param boolean $soft.
     *
     * @return string.
     */
    protected function get($url, $soft = true)
    {
        if ($soft) {
            return $this->getSoft($url);
        } else {
            return $this->getHard($url);
        }
    }

    /**
     * Get page with file get content.
     *
     * @param $url.
     *
     * @return string.
     */
    private function getHard($url)
    {
        $content = file_get_contents($url);
        return $content;
    }
}
