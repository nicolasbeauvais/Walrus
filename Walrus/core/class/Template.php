<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 09:13 28/01/14
 */
 

namespace Walrus\core\entity;


class Template
{

    /**
     * Template name
     * @var string
     */
    protected $name = '';

    /**
     * Path for the template, relative to FRONT_PATH constant.
     * @var string
     */
    protected $template = '';

    /**
     * Variables of the template
     * @var array
     */
    protected $variables = array();

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param array $variables
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
}

