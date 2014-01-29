<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 09:13 28/01/14
 */

namespace Walrus\core\objects;

class Template
{

    /**
     * Template name
     * @var string
     */
    protected $name = '';

    /**
     * Alias for the template
     * @var string
     */
    protected $alias = '';

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
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
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

