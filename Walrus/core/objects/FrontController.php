<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 08:08 05/02/14
 */

namespace Walrus\core\objects;

/**
 * Class FrontController
 * @package Walrus\core\objects
 */
class FrontController
{

    /**
     * Stores all template of a WalrusController instance.
     *
     * @var array
     */
    private $templates = array();

    /**
     * Stores all variables of a WalrusController instance.
     *
     * @var array
     */
    private $variables = array();

    /**
     * @param array $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }


}
