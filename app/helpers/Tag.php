<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 19:00 27/03/14
 */

namespace app\helpers;

use Walrus\core\WalrusException;

/**
 * Class Tag
 * @package Walrus\core\objects
 */
class Tag
{
    /**
     * @var string HTML tag
     */
    private $tag;

    /**
     * @var array attributes
     */
    private $attributes = array();

    /**
     * @var array self closed tags list
     */
    private $self_closers = array('input', 'img', 'hr', 'br', 'meta', 'link');

    /**
     * empty constructor
     */
    public function __construct ()
    {
    }

    /**
     * Construct a HTML Tag.
     *
     * @param string $tag the tag name
     * @param array $self_closers element which are self closed
     *
     * @throws WalrusException if $self_closer isn't an array
     *
     * @return Tag
     */
    public function create($tag, $self_closers = null)
    {
        $this->tag = strtolower($tag);

        if (isset($self_closers) && !is_array($self_closers)) {
            throw new WalrusException('$self_closers must be an array of self_closing tag');
        }

        if (isset($self_closers)) {
            $this->self_closers = $self_closers;
        }

        return $this;
    }

    /**
     * Return all the attributes of the current element or a specified one.
     *
     * @param string $attribute
     * @return mixed a single attribute if specified or an array of all attributes
     *
     * @throws WalrusException if the specified attribute doesn't exist
     */
    public function getAttributes($attribute = null)
    {
        if (isset($attribute)) {
            if (isset($this->attributes[$attribute])) {
                return $this->attributes[$attribute];
            } else {
                throw new WalrusException('The attribute ' . $attribute . ' doesn\'t exist');
            }
        }

        return $this->attributes;
    }

    /**
     * Add one attribute or an array of attributes to the current tag.
     *
     * @param mixed $attribute
     * @param string $value
     *
     * @return Tag
     */
    public function setAttributes($attribute, $value = '')
    {
        if (!is_array($attribute)) {
            $this->attributes[$attribute] = $value;
        } else {
            $this->attributes = array_merge($this->attributes, $attribute);
        }

        return $this;
    }

    /**
     * Remove the specified attribute or all if specified.
     *
     * @param mixed $attribute the attribute to remove or if set to true remove all attributes
     *
     * @return Tag
     */
    public function removeAttributes($attribute)
    {
        if ($attribute === true) {
            $this->attributes = array();
            return;
        }

        if (isset($this->attributes[$attribute])) {
            unset($this->attributes[$attribute]);
        }

        return $this;
    }

    /**
     * Add a Tag HTML tag into an other.
     *
     * @param $object
     *
     * @return Tag
     */
    public function inject(Tag $object)
    {
        if (get_class($object) == __class__) {
            $this->attributes['text'] .= $object->make();
        }

        return $this;
    }

    /**
     * Create the HTML tag and return it as a string.
     *
     * @return string the HTML output
     */
    public function make()
    {
        $build = '<' . $this->tag;

        if (count($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                if ($key != 'text') {
                    $build .= ' ' . $key . ($value === false ? '' : '="' . $value . '"');
                }
            }
        }

        if (!in_array($this->tag, $this->self_closers)) {
            $build .= '>' . (isset($this->attributes['text']) ? $this->attributes['text'] : '')
                . '</' . $this->tag . '>';
        } else {
            $build .= ' />';
        }

        return $build;
    }
}
