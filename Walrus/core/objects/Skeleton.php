<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 09:13 28/01/14
 */

namespace Walrus\core\objects;

/**
 * Class Skeleton
 * @package Walrus\core\objects
 */
class Skeleton
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
    protected $templates = array();

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
     * @param array $templates
     */
    public function setTemplate($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @param Template $template
     */
    public function addTemplate(Template $template)
    {
        $this->templates[] = $template;
    }

    /**
     * @return string
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
