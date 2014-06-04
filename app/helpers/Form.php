<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 08:09 26/03/14
 */

namespace app\helpers;

use Walrus\core\WalrusHelpers;

/**
 * Class Form
 * @package Walrus\core\helpers
 */
class Form
{
    /**
     * @var string form action for HTML output
     */
    private $action = '';

    /**
     * @var string form method for HTML output
     */
    private $method = 'POST';

    /**
     * @var array Accepted HTTP methods
     */
    private $methods = array('GET','POST','PUT','DELETE');

    /**
     * @var array attributes to add to the form HTML output
     */
    private $attributes = array();

    /**
     * @var array of form elements
     */
    private $fields = array();
    /**
     * @var string content to put inside the form HTML element
     */
    private $content = '';

    /**
     * Form constructor
     */
    public function __construct()
    {

    }

    /**
     * Add a field to the form.
     *
     * @param Tag|string $tag a Tag object, or a string representing the element name attribute
     *
     */
    public function addField ($tag)
    {
        // @TODO: !
        // check if string or Tag class, else throw Exception
        if (is_string($tag)) {
            $this->fields[$tag] = '';
        } elseif (gat_class($tag) == 'Tag') {
            $this->fields[$tag->getAttributes('name')] = '';
        }


    }

    /**
     * Add HTML content to the form.
     *
     * @param string $content HTML content to put inside
     * @param bool $before set to true to add the content before Form elements
     */
    public function inject($content, $before = false)
    {
        $this->content = $before ? $content . $this->content : $this->content . $content;
    }

    /**
     * Return the HTML for the form
     */
    public function make()
    {
        return WalrusHelpers::getHelper('Tag')
            ->create('form')
            ->setAttributes(array(
                'method' => $this->method,
                'action' => $this->action,
                'text' => $this->content
            ))
            ->make();
    }

    /**
     * @param $action the action to set
     * @return Form
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string $action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $attributes the attributes to set
     * @return Form
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return string $action
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
