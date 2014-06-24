<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 10:46 20/06/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

/**
 * Class WalrusI18n
 * @package Walrus\core
 */
class WalrusI18n
{
    /**
     * @var array all i18n text.
     */
    private static $values;

    /**
     * Private construct to prevent multiples instances.
     */
    private function __construct()
    {
    }

    /**
     * Private clone to prevent multiples instances.
     *
     * @throws WalrusException if the selected language doesn't exist
     */
    private function __clone()
    {
    }

    /**
     * @throws WalrusException
     */
    public static function initialise()
    {
        $lang = $_ENV['W']['language'];

        if (!isset($_ENV['W']['i18ns'][$lang])) {
            throw new WalrusException('The selected language: "' .  $lang . '" cannot be found');
        }

        self::$values  = $_ENV['W']['i18ns'][$lang];
    }

    /**
     * @return array
     * @throws WalrusException
     */
    public static function get()
    {
        $args = func_get_args();

        $value = self::$values;

        if (empty($args)) {
            return $value;
        }

        foreach ($args as $arg) {
            if (isset($value[$arg])) {
                $value = $value[$arg];
            } else {
                throw new WalrusException('Cannot find translation for: ' . explode('/', $args));
            }
        }

        return $value;
    }
}
