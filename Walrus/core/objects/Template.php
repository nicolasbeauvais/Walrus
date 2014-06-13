<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 09:13 28/01/14
 */

namespace Walrus\core\objects;

/**
 * Class Template
 * @package Walrus\core\objects
 */
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
     * Walrus core template are always in php templating.
     *
     * Walrus core tempalte deserve a special treatment as they are in .php templating
     * even if you use smarty or haml
     */
    protected $isWalrus = false;

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
     * @param boolean $isWalrus
     */
    public function setIsWalrus($isWalrus)
    {
        $this->isWalrus = $isWalrus;
    }

    /**
     * @return boolean
     */
    public function getIsWalrus()
    {
        return $this->isWalrus;
    }


}
