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
        //@TODO: check config for YAML / PHP mode
        $this->getRoutesFromYAML();
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

        return $this->match($requestUrl, $requestMethod);
    }

    /**
     * Match given request url and request method and see if a route has been defined for it
     * If so, return route's target
     * If called multiple times
     */
    public function match($requestUrl, $requestMethod = 'GET')
    {
        foreach ($this->routes as $route) {

            // compare server request method with route's allowed http methods
            if (!in_array($requestMethod, $route->getMethods())) {
                continue;
            }

            // check if request url matches route regex. if not, return false.
            if (!preg_match("@^".$route->getRegex()."*$@i", $requestUrl, $matches)) {
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
     * Special Walrus router.
     * Make use of pux with YAML functionality.
     */
    public function getRoutesFromYAML()
    {
        $this->setBasePath('/');
        $this->map('/', 'someController:indexAction', array('methods' => 'GET'));
        //load routes
        $route = $this->matchCurrentRequest();
        
        /*
        $url = isset($_GET['url']) ? '/' . rtrim($_GET['url'], "/") : '/';

        //Load YAML routes
        if (file_exists('../config/routes.yml')) {
            $routes = Spyc::YAMLLoad('../config/routes.yml');
        } else {
            throw new Exception("Can't find routes.yml in config directory");
        }

        //Transform YAML routes to valid Pux routes
        $walrusRoutes = new Mux();

        foreach ($routes as $route) {

            $method = isset($route['method']) ? strtolower($route['method']) : 'add';
            $path = isset($route['path']) ? $route['path'] : '';
            $controller = isset($route['controller']) ? $route['controller'] : '';
            $action = isset($route['action']) ? $route['action'] : '';
            $params = isset($route['params']) ? $route['params'] : array();

            $methodConstant = $walrusRoutes->getRequestMethodConstant($method);
            if ($methodConstant != 0) {
                $params['method'] = $methodConstant;
            }

            $walrusRoutes->add($path, array($controller, $action), $params);
        }

        //check current route
        $dispatched = $walrusRoutes->dispatch($url);

        // create the reflection class
        if (class_exists('engine\controllers\\' . $dispatched[2][0], true)) {
            $dispatched[2][0] = 'engine\controllers\\' . $dispatched[2][0];
        } elseif (class_exists('Walrus\controllers\\' . $dispatched[2][0], true)) {
            $dispatched[2][0] = 'Walrus\controllers\\' . $dispatched[2][0];
        } else {
            throw new Exception('Requested route doesn\'t exist');
        }

        //Execute route
        try {
            Executor::execute($dispatched);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
        */
    }
}