<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:59 27/01/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;
use ReflectionClass;

/**
 * Class WalrusModel
 * @package Walrus\core
 */
class WalrusModel
{

    /*
     * Array of models instance
     */
    private $models = array();

    /**
     * Empty for now.
     */
    public function __construct()
    {

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
}
