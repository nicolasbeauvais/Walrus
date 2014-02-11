<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 20:46 15/01/14
 */

namespace Walrus\core;

use Spyc\Spyc;
use Walrus\core\objects\Route;
use Exception;
use ReflectionClass;

/**
 * Class WalrusRoute
 * @package Walrus\core
 */
class WalrusRouter
{
    /**
     * Array that holds all Route objects
     * @var array
     */
    private $routes = array();

    /**
     * Array to store named routes in, used for reverse routing.
     * @var array
     */
    private $namedRoutes = array();

    /**
     * String to store the current URL
     * @var string
     */
    private $currentPath = '';

    /**
     * The base REQUEST_URI. Gets prepended to all route url's.
     * @var string
     */
    private $basePath = '';

    /**
     * Set the base url - gets prepended to all route url's.
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = (string) $basePath;
    }

    /**
     * The WalrusRouter unique instance for singleton.
     * @var WalrusRouter
     */
    protected static $instance;

    /**
     * Private construct to prevent multiples instances
     */
    protected function __construct()
    {
    }

    /**
     * Private clone to prevent multiples instances
     */
    protected function __clone()
    {
    }

    /**
     * Main function to call to get an instance of WalrusRouter.
     * @return WalrusRouter
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Launch the Walrus routing.
     *
     * test if it's an API route or not.
     * API route demand less process execution.
     */
    public function execute()
    {
        $this->setBasePath('/');

        try {

            $url = isset($_GET['url']) ? $_GET['url'] : '/';
            if (preg_match('@^api/@', $url)) {
                $this->processForAPI();
            } else {
                session_start();
                $this->getRoutesFromYAML();
                $this->process();
            }
        } catch (Exception $e) {
            header("Status: 404 Not Found");
            header('HTTP/1.0 404 Not Found');
            die();
        }
    }

    /**
     * Soft Walrus routing.
     * Used by Walrus routing for get soft
     */
    public function softExecute()
    {
        $this->setBasePath('/');

        try {
            $this->process();
        } catch (Exception $e) {
            header("Status: 404 Not Found");
            header('HTTP/1.0 404 Not Found');
            die();
        }
    }

    /**
     * Route factory method.
     * Maps the given URL to the given target
     *
     * @param string $routeUrl string
     * @param mixed $target The target of this route: can be anything.
     * @param array $args Array of optional arguments.
     */
    public function map($routeUrl, $target = '', array $args = array())
    {
        $route = new Route();

        $route->setUrl($this->basePath . $routeUrl);

        $route->setTarget($target);

        if (isset($args['methods'])) {
            $route->setMethod($args['methods']);
        }

        if (isset($args['filters'])) {
            $route->setFilters($args['filters']);
        }

        if (isset($args['name'])) {
            $route->setName($args['name']);
            if (!isset($this->namedRoutes[$route->getName()])) {
                $this->namedRoutes[$route->getName()] = $route;
            }
        }

        if (isset($args['acl'])) {
            $route->setAcl($args['acl']);
        }

        $this->routes[] = $route;
    }

    /**
     * Matches the current request against mapped routes
     */
    public function matchCurrentRequest()
    {
        $checkMethod  = isset($_POST['_method'])
            && ($_method = strtoupper($_POST['_method']))
            && in_array($_method, array('PUT', 'DELETE'));

        $requestMethod = $checkMethod ? $_method : $_SERVER['REQUEST_METHOD'];
        $requestUrl = isset($_GET['url']) ? $_GET['url'] : '/';


        // strip GET variables from URL
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl =  substr($requestUrl, 0, $pos);
        }

        $this->currentPath = '/' . rtrim($requestUrl, '/').'/';

        return $this->match($requestMethod);
    }

    /**
     * Match given request url and request method and see if a route has been defined for it.
     * If so, return route's target
     * If called multiple times
     *
     * @param string $requestMethod The type of methode for the route.
     *
     * @return Route|false
     */
    public function match($requestMethod = 'GET')
    {
        foreach ($this->routes as $route) {
            // compare server request method with route's allowed http methods
            if (!in_array($requestMethod, $route->getMethods())) {
                continue;
            }

            // check if request url matches route regex. if not, return false.
            if (!preg_match("@^".$route->getRegex()."*$@i", $this->currentPath, $matches)) {
                continue;
            }

            $params = array();

            if (preg_match_all("/:([\w-]+)/", $route->getUrl(), $argument_keys)) {

                // grab array with matches
                $argument_keys = $argument_keys[1];

                // check params validity
                $paramsToCheck = $route->getFilters();

                // loop trough parameter names, store matching value in $params array
                foreach ($argument_keys as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];

                        if (isset($paramsToCheck['require']) && isset($paramsToCheck['require'][$name])) {
                            $regex = $paramsToCheck['require'][$name];

                            //test regex pattern
                            if (!preg_match_all('@' . $regex . '@', $matches[$key + 1], $test)) {

                                //else apply default value
                                if (isset($paramsToCheck['default']) && isset($paramsToCheck['default'][$name])) {
                                    $params[$name] = $paramsToCheck['default'][$name];
                                } else {
                                    return false;
                                }
                            }
                        }
                    }
                }
            }

            $route->setParameters($params);

            return $route;
        }
        return false;
    }

    /**
     * Reverse route a named route.
     *
     * @param string $routeName The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL.
     *
     * @throws Exception
     * @return string The url to the route
     */
    public function generate($routeName, array $params = array())
    {
        // Check if route exists
        if (!isset($this->namedRoutes[$routeName])) {
            throw new Exception("No route with the name $routeName has been found.");
        }

        $route = $this->namedRoutes[$routeName];
        $url = $route->getUrl();

        // replace route url with given parameters
        if ($params && preg_match_all("/:(\w+)/", $url, $param_keys)) {

            // grab array with matches
            $param_keys = $param_keys[1];

            // loop trough parameter names, store matching value in $params array
            foreach ($param_keys as $key) {
                if (isset($params[$key])) {
                    $url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
                }
            }
        }
        return $url;
    }

    /**
     * Match routes, make verification on controller and action.
     *
     * @throws Exception
     */
    private function process()
    {
        $route = $this->matchCurrentRequest();
        if (!$route) {
            $url = isset($_GET['url']) ? $_GET['url'] : '/';
            throw new Exception('[WalrusRouting] undefined route: ' . $url);
        }

        if ($route->getAcl() && (!isset($_SESSION['acl']) || $route->getAcl() != $_SESSION['acl'])) {
            header("Status: 403 Forbidden");
            header('HTTP/1.0 403 Forbidden');
            die();
        }

        $cb = explode(':', $route->getTarget());

        if (count($cb) === 2) {
            $controller = $cb[0];
            if (empty($controller)) {
                throw new Exception('[WalrusRouting] empty route controller');
            }
            $action = $cb[1];
            if (empty($action)) {
                throw new Exception('[WalrusRouting] empty route action');
            }
        } else {
            throw new Exception('[WalrusRouting] invalid route target: "' . $route->getTarget() . '"');
        }

        $class = WalrusAutoload::getNamespace($controller);

        if (!$class) {
            throw new Exception('[WalrusRouting] Can\'t load class: ' . $controller);
        }

        $rc = new ReflectionClass($class);

        // Create the controller object.
        $cb[0] = $rc->newInstance();

        // check controller action method
        if ($cb[0] && ! method_exists($cb[0], $cb[1])) {
            throw new Exception('Controller exception');
        }

        $rps = $rc->getMethod($cb[1])->getParameters();

        $filters = $route->getParameters();
        $vars = isset($filters) ? $filters : array();
        $arguments = array();

        foreach ($rps as $param) {
            $n = $param->getName();

            $default = $route->getFilters();
            $default = isset($default) && isset($default['default'][ $n ]) ? $default['default'][ $n ] : false;
            if (isset($vars[ $n ])) {
                $arguments[] = $vars[ $n ];
            } elseif ($default) {
                $arguments[] = $default;
            } elseif (!$param->isOptional() && !$param->allowsNull()) {
                throw new Exception('parameter is not defined.');
            }
        }

        return call_user_func_array($cb, $arguments);
    }

    /**
     * Match for API
     */
    private function processForAPI()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '/';
        $apiUrl = rtrim(str_replace('api/', '', $url), '/');

        $cb = explode('/', $apiUrl);

        if (count($cb) < 2) {
            $controller = $cb[0];
            if (empty($controller)) {
                throw new Exception('[WalrusRouting] empty API controller');
            }
            $action = $cb[1];
            if (empty($action)) {
                throw new Exception('[WalrusRouting] empty API action');
            }
        }

        $class = 'engine\\api\\' . ucwords($cb[0]) . 'Controller';

        if (class_exists($class)) {

            $rc = new ReflectionClass($class);

            // Create the controller object.
            $cb[0] = $rc->newInstance();

            // check controller action method
            if ($cb[0] && ! method_exists($cb[0], $cb[1])) {
                throw new Exception('Controller exception');
            }

            $rps = $rc->getMethod($cb[1])->getParameters();

            // @TODO: add args to API call
            $arguments = array();

            WalrusAPI::init();
            WalrusAPI::execute(call_user_func_array($cb, $arguments));
        } else {
            header("Status: 404 Not Found");
            header('HTTP/1.0 404 Not Found');
        }
    }

    /**
     * Reroute to a new controller.
     *
     * Reroute from a controller / action couple in string format.
     * There is no params test on this function, use it carefully.
     *
     * @param string $controller a controller name
     * @param string $action an action of the controller
     * @param array $param an array of the parameter to pass to the controller
     *
     * @throws Exception
     */
    public static function reroute($controller, $action, $param = array())
    {
        $controllerClass = ucwords(strtolower($controller)) . 'Controller';

        $class = WalrusAutoload::getNamespace($controllerClass);

        if (!$class) {
            throw new Exception('[WalrusFrontController] Can\'t load controller: ' . $controller);
        }

        $cb[] = array();
        $cb[0] = $controller;
        $cb[1] = $action;
        $rc = new ReflectionClass($class);

        // Create the controller object.
        $cb[0] = $rc->newInstance();

        // check controller action method
        if ($cb[0] && ! method_exists($cb[0], $cb[1])) {
            throw new Exception('Controller exception');
        }

        call_user_func_array($cb, $param);
    }

    /**
     * Special Walrus router.
     * Make use of pux with YAML functionality.
     *
     * @throws Exception
     */
    public function getRoutesFromYAML()
    {

        //Load YAML routes
        if (file_exists('../config/routes.yml')) {
            $routes = Spyc::YAMLLoad('../config/routes.yml');
        } else {
            throw new Exception("Can't find routes.yml in config directory");
        }

        foreach ($routes as $name => $route) {

            $path = isset($route['path']) ? $route['path'] : '/';
            $controller = isset($route['controller']) ? $route['controller'] : '';
            $action = isset($route['action']) ? $route['action'] : '';

            $toCall = $controller . ':' . $action;
            $params = array();

            $params['name'] = $name;

            if (isset($route['method']) && !empty($route['method'])) {
                $params['methods'] = isset($route['method']) ? strtoupper($route['method']) : 'GET';
            } else {
                $params['methods'] = 'GET';
            }
            if (isset($route['filters']) && !empty($route['filters'])) {
                $params['filters'] = isset($route['filters']) ? $route['filters'] : array();
            }
            if (isset($route['acl']) && !empty($route['acl'])) {
                $params['acl'] = isset($route['acl']) ? $route['acl'] : '';
            }

            $this->map($path, $toCall, $params);
        }
    }
}
