<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 00:29 08/02/14
 */

namespace Walrus\core;

use Exception;

/**
 * Class WalrusAPI
 * @package Walrus\core
 */
class WalrusAPI
{

    /**
     * Init an API process
     */
    public static function init()
    {
        // @TODO: API init
    }

    /**
     * Display API process result
     * @param $data API data
     */
    public static function execute($data)
    {

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
     * Return an instance of the specified model
     *
     * @param string $model
     *
     * @throws Exception if the model doesn't exist
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
            throw new Exception('[WalrusFrontController] request unexistant model: ' . $modelClass);
        }

        $modelInstance = new $modelClassWithNamespace();
        $this->models[$modelClass] = $modelInstance;

        return $modelInstance;
    }
}
