<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 20:46 15/01/14
 */

//@TODO: Add singleton patten
namespace Walrus\core;

use Spyc\Spyc as Spyc;
use Walrus\core\entity\Route as Route;
use Exception;
use Reflection;

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
     * @param string $base_url
     */
    public function setBasePath($basePath)
    {
        $this->basePath = (string) $basePath;
    }


    /**
     * Launch the Walrus routing.
     * Check the actual configuration to found YAML or PHP files
     */
    public function execute()
    {
        $this->setBasePath('/');

        //@TODO: check config for YAML / PHP mode
        $this->getRoutesFromYAML();

        try {
            $this->process();
        } catch (Exception $e) {
            echo $e->getMessage();
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
            $methods = explode(',', $args['methods']);
            $route->setMethods($methods);
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

        $this->currentPath = '/' . $requestUrl;

        return $this->match($requestMethod);
    }

    /**
     * Match given request url and request method and see if a route has been defined for it
     * If so, return route's target
     * If called multiple times
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

                // loop trough parameter names, store matching value in $params array
                foreach ($argument_keys as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }

            }

            $route->setParameters($params);

            return $route;
        }
        return false;
    }


    /**
     * Reverse route a named route
     *
     * @param string $route_name The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL
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
            foreach ($param_keys as $i => $key) {
                if (isset($params[$key])) {
                    $url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
                }
            }
        }
        return $url;
    }

    /**
     * Match routes, make verification on controller and action.
     */
    public function process()
    {
        // @TODO: check method, name, filters, parameters
        $route = $this->matchCurrentRequest();

        if (!$route) {
            throw new Exception('[WalrusRouting] undefined route: ' . isset($_GET['url']) ? $_GET['url'] : '/');
        }

        // sanitize ?
        if ($route->getMethods() === 'GET') {
            $_POST[] = array();
        }

        $toCall = explode(':', $route->getTarget());

        if (count($toCall) === 2) {
            $controller = $toCall[0];
            if (empty($controller)) {
                throw new Exception('[WalrusRouting] empty route controller');
            }
            $action = $toCall[1];
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

        $rc = new \ReflectionClass($class);

        $cb = array($controller, $action);
        $args = null;

        // if the first argument is a class name string,
        // then create the controller object.
        if (is_string($cb[0])) {
            $cb[0] = $controller = $args ? $rc->newInstanceArgs($args) : $rc->newInstance();
        } else {
            $controller = $cb[0];
        }

        // check controller action method
        if ($controller && ! method_exists($controller, $cb[1])) {
            throw new Exception('Controller exception');
        }

        $rps = $rc->getMethod($cb[1])->getParameters();

        $filters = $route->getParameters();
        $vars = isset($filters) ? $filters : array();
        $arguments = array();

        foreach ($rps as $param) {
            $n = $param->getName();
            if (isset($vars[ $n ])) {
                $arguments[] = $vars[ $n ];
            } elseif (isset($route['default'][ $n ])
                && $default = $route['default'][ $n ]) {
                $arguments[] = $default;
            } elseif (!$param->isOptional() && !$param->allowsNull()) {
                throw new Exception('parameter is not defined.');
            }
        }

        return call_user_func_array($cb, $arguments);
    }

    /**
     * Special Walrus router.
     * Make use of pux with YAML functionality.
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
            }
            if (isset($route['filters']) && !empty($route['filters'])) {
                $params['filters'] = isset($route['filters']) ? $route['filters'] : array();
            }

            $this->map($path, $toCall, $params);
        }
    }
}
