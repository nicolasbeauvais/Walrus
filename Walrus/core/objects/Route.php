<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 23:10 29/01/14
 */

namespace Walrus\core\objects;

/**
 * Class Route
 * @package Walrus\core\objects
 */
class Route
{

    /**
    * URL of this Route
    * @var string
    */
    private $url;

    /**
    * Accepted HTTP methods for this route
    * @var array
    */
    private $methods = array('GET','POST','PUT','DELETE');

    /**
     * @var string
     */
    private $method = 'GET';

    /**
    * Target for this route, can be anything.
    * @var mixed
    */
    private $target;

    /**
    * The name of this route, used for reversed routing
    * @var string
    */
    private $name;

    /**
    * Custom parameter filters for this route
    * @var array
    */
    private $filters = array();

    /**
    * Array containing parameters passed through request URL
    * @var array
    */
    private $parameters = array();

    /**
     * The acl of this route
     * @var string
     */
    private $acl = false;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $url = (string) $url;

        // make sure that the URL is suffixed with a forward slash
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return mixed
     */
    public function getRegex()
    {

        $regXpUrl = preg_replace_callback("(\(:(\w+)\)/)", array(&$this, 'substituteOptionalFilter'), $this->url);
        $regXpUrl = preg_replace_callback("/:(\w+)/", array(&$this, 'substituteFilter'), $regXpUrl);
        return rtrim($regXpUrl, '/').'/';
    }

    /**
     * @param $matches
     * @return string
     */
    private function substituteFilter($matches)
    {
        if (isset($matches[1]) && isset($this->filters['require'][$matches[1]])) {

            return '(' . $this->filters['require'][$matches[1]] . ')';
        }

        return "([\w-]+)";
    }

    /**
     * @param $matches
     * @return string
     */
    private function substituteOptionalFilter($matches)
    {
        if (isset($matches[1]) && isset($this->filters['require'][$matches[1]])) {
            return '(' . $this->filters['require'][$matches[1]] . ')?/?';
        }

        return "([\w-]+)?/?";
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param mixed $acl
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return mixed
     */
    public function getAcl()
    {
        return $this->acl;
    }
}
