<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 19:18 17/03/14
 */

namespace app\helpers;


/**
 * Class Url
 * @package Walrus\core\helpers
 */
class Url
{

    /**
     * Return a base 64 encoded $url in a data-nolink attribute.
     *
     * @param string $url the url to encode
     *
     * @return string encoded url
     */
    public function nolink($url)
    {
        echo 'data-nolink="' . base64_encode($url) . '"';
    }
}
