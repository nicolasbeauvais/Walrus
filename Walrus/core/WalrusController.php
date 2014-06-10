<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:59 27/01/14
 */

namespace Walrus\core;

use Walrus\core\objects\Skeleton;
use Walrus\core\objects\Template;
use Walrus\core\objects\FrontController;
use Walrus\core\WalrusException;
use ReflectionClass;
use MtHaml;
use Smarty;

/**
 * Class WalrusController
 * @package Walrus\core
 */
class WalrusController
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
     * Store frontController instance as a stack
     *
     * @var array
     */
    private $frontController;

    /**
     * Array of models instance
     */
    protected $models;

    /**
     * Array of instancied controllers
     */
    private $controllers;

    /**
     * Init templating variables.
     *
     * Register all needed variables like _ENV and Helpers
     */
    public function __construct()
    {
        switch ($_ENV['W']['templating']) {
            case 'haml':
                self::$templating[0] = '.haml';
                self::$templating[1] = '.php';
                break;
            case 'smarty':
                self::$templating[0] = '.tpl';
                self::$templating[1] = '';
                break;
            case 'php':
                self::$templating[0] = '.php';
                self::$templating[1] = '';
        }

        // Globals
        $this->register('_ENV', $_ENV);
        $this->register('helpers', WalrusHelpers::execute());
    }

    /**
     * Add a template to the stack.
     *
     * Add a template to the template stack with ACL or not,
     * the stack is displayed by WalrusController::execute()
     * at the end of Walrus execution.
     * setView as a very special treatment for all template called from
     * a Walrus core controller
     *
     * @param string $view the template to add on templates stack
     * @param bool|string $acl
     *
     * @throws WalrusException
     */
    protected function setView($view, $acl = false)
    {
        $className = explode('\\', get_called_class());

        if (strrpos($view, '/') === false && strrpos($view, '\\') === false) {
            $controller = strtolower(str_replace('Controller', '', end($className)));
            $template = $_ENV['W']['FRONT_PATH'] . $controller . DIRECTORY_SEPARATOR . $view . self::$templating[0];
        } else {
            $template = $_ENV['W']['FRONT_PATH'] . $view . self::$templating[0];
        }

        if ($acl && (!isset($_SESSION['acl']) || $acl != $_SESSION['acl'])) {
            return;
        }

        $objTemplate = new Template();

        if ($className[0] === 'Walrus') {
            $template = isset($controller) ? $_ENV['W']['ROOT_PATH'] . 'Walrus' . DIRECTORY_SEPARATOR
                . 'templates' . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $view . '.php'
                : $_ENV['W']['ROOT_PATH'] . 'Walrus' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR
                . $view . '.php';
            $objTemplate->setIsWalrus(true);
        }

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
     * @throws WalrusException
     */
    protected function register($key, $var)
    {
        if (!isset($key) || !isset($var)) {
            throw new WalrusException('Missing argument for function register');
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
        if ($_ENV['W']['templating'] == 'smarty') {
            self::$smarty = new Smarty();
            self::$smarty->setCacheDir($_ENV['W']['CACHE_PATH'] . 'smarty')
                ->setCompileDir($_ENV['W']['CACHE_PATH'] . 'smarty')
                ->setTemplateDir($_ENV['W']['APP_PATH'] . 'templates');
        }

        if (count(self::$variables) > 0) {
            foreach (self::$variables as self::$foreach_key => self::$foreach_value) {
                ${self::$foreach_key} = self::$foreach_value;
                if ($_ENV['W']['templating'] == 'smarty') {
                    self::$smarty->assign(self::$foreach_key, self::$foreach_value);
                }
            }
        }

        if (count(self::$templates) > 0) {

            foreach (self::$templates as self::$foreach_key => self::$foreach_value) {
                if (self::$foreach_value->getIsWalrus()) {
                    require(self::$foreach_value->getTemplate());
                    continue;
                }
                switch ($_ENV['W']['templating']) {
                    case 'haml':
                        self::compileToHAML(self::$foreach_value);
                        require(self::$foreach_value->getTemplate());
                        break;
                    case 'smarty':
                        self::$smarty->display(self::$foreach_value->getTemplate());
                        break;
                    default:
                        require(self::$foreach_value->getTemplate());
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
     * Load skeletons.
     */
    private function loadSkeletons()
    {
        $skeletons = $_ENV['W']['skeletons'];

        foreach ($skeletons as $skeletonName => $skeleton) {

            $templates = array();

            foreach ($skeleton as $name => $value) {

                $template = $_ENV['W']['FRONT_PATH'] . $value['template'] . self::$templating[0];

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
     * Compile HAML file.
     *
     * @param Template $template
     *
     * @throws WalrusException
     */
    private static function compileToHAML ($template)
    {
        $templateName = substr($template->getTemplate(), 0, -4);
        $templateDir = substr($templateName, 0, strrpos($templateName, DIRECTORY_SEPARATOR) - strlen($templateName));

        $haml = new MtHaml\Environment('php');

        if (!file_exists($templateName)) {
            throw new WalrusException('Requested template does not exist: ' . $templateName);
        }

        $hamlCode = file_get_contents($templateName);


        if (!file_exists($templateName . '.php') || filemtime($templateName . '.php') != filemtime($templateName)) {

            $phpCode = $haml->compileString($hamlCode, $templateName);

            $tempFile = tempnam($templateDir, 'haml');
            file_put_contents($tempFile, $phpCode);

            copy($tempFile, $templateName . '.php');
            rename($tempFile, $templateName . '.php');
            touch($templateName.'.php', filemtime($templateName));
        }
    }

    /**
     * Return an instance of the specified controller
     *
     * @param string $controller
     *
     * @return Class the specified controller class
     * @throws WalrusException if the controller doesn't exist
     */
    protected function controller($controller)
    {
        $controllerClass = ucwords(strtolower($controller)) . 'Controller';

        if (isset($this->controllers[$controllerClass])) {
            return $this->controllers[$controllerClass];
        }

        $controllerClassWithNamespace =  WalrusAutoload::getNamespace($controllerClass);

        if (!$controllerClassWithNamespace) {
            throw new WalrusException('Request unexistant controller: ' . $controllerClass);
        }

        $controllerInstance = new $controllerClassWithNamespace();
        $this->controllers[$controllerClass] = $controllerInstance;

        return $controllerInstance;
    }

    /**
     * Return an instance of the specified model
     *
     * @param string $model
     *
     * @throws WalrusException if the model doesn't exist
     * @return Class the specified model class
     */
    protected function model($model)
    {
        $modelClass = ucwords(strtolower($model));

        if (isset($this->models[$modelClass])) {
            return $this->models[$modelClass];
        }

        $modelClassWithNamespace =  WalrusAutoload::getNamespace($modelClass);

        if (!$modelClassWithNamespace) {
            throw new WalrusException('Request unexistant model: ' . $modelClass);
        }

        $refl = new ReflectionClass($modelClassWithNamespace);
        $refl->getConstructor();

        $modelInstance = new $modelClassWithNamespace();
        $this->models[$modelClass] = $modelInstance;

        return $modelInstance;
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
     * Reroute to a new controller.
     *
     * the reroute action clean all the WalrusController.
     * the controller / action don't need to be accessible with classic routing.
     *
     * @param string $controller a controller name
     * @param string $action an action of the controller
     * @param array $param an array of the parameter to pass to the controller
     *
     * @throws WalrusException
     */
    protected function reroute($controller, $action, $param = array())
    {
        $this->uload();
        WalrusRouter::reroute($controller, $action, $param);
        self::execute();
        die;
    }

    /**
     * Get page with file get content.
     *
     * @param $url.
     *
     * @return string.
     */
    protected function getHard($url)
    {
        $content = file_get_contents($url);
        return $content;
    }

    /**
     * Process a new controller and return his content.
     *
     * the reroute action doesn't clean WalrusController.
     * All your previously stored template / skeleton and variables
     * are restored.
     * the controller / action don't need to be accessible with classic routing.
     *
     * @param string $controller a controller name
     * @param string $action an action of the controller
     * @param array $param an array of the parameter to pass to the controller
     *
     * @return string the content made by the getted controller
     * @throws WalrusException
     */
    protected function getSoft($controller, $action, $param = array())
    {
        $this->stackFrontController();
        $this->uload();

        ob_start();
        WalrusRouter::reroute($controller, $action, $param);
        self::execute();
        $content = ob_get_contents();
        ob_end_clean();
        $this->unstackFrontController();

        return $content;
    }

    /**
     * Add the current state of the FrontController to the stack.
     */
    private function stackFrontController()
    {
        $objFrontController = new FrontController();
        $objFrontController->setTemplates(self::$templates);
        $objFrontController->setVariables(self::$variables);

        $this->frontController[] = $objFrontController;
    }

    /**
     * Remove the last stored state of the FrontController from the stack.
     */
    private function unstackFrontController()
    {
        $objFrontController = array_pop($this->frontController);
        self::$templates = $objFrontController->getTemplates();
        self::$variables = $objFrontController->getVariables();
    }

    /**
     * Reset all variables from the WalrusController class.
     */
    private function uload()
    {
        self::$templates = array();
        self::$variables = array();
    }
}
