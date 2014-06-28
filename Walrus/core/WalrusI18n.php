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

        $vars = '';
        $format = '';
        $message = self::$values;

        if (empty($args)) {
            return $message;
        }

        if(substr_count($args[0], '.'))
        {
            $params = explode('.', $args[0]);
            if(!empty($args[1]))
                $params[] = $args[1];

            $args = $params;
        }

        foreach ($args as $arg) {

            if (is_array($arg)) {
                $vars = $arg;
                continue;
            }

            if (isset($message['format'])) {
                $format = $message['format'];
            }

            if (isset($message[$arg])) {
                $message = $message[$arg];
            } else {

                foreach ($args as $key => $arg) {
                    if (is_array($arg)) {
                        unset($args[$key]);
                    }
                }
                throw new WalrusException('Cannot find translation for: ' . implode('/', $args));
            }
        }

        // handle singular and plural
        if (is_array($message)) {
            if (isset($vars['count']) && ($vars['count'] > 1 && isset($message['other']))
             || ($vars['count'] <= 1 && isset($message['one']))) {
                $message = $vars['count'] > 1 ? $message['other'] : $message['one'];
            } else {
                throw new WalrusException('Expected count vars and one/other i18n to process: "' . $message  . '"');
            }
        }

        if (!empty($format) && !empty($vars)) {
            foreach ($vars as $key => $var) {
                $format = preg_replace("/\%\{(" . $key . ")\}/", $var, $format);
            }

            $value = preg_replace("/\%\{(message)\}/", $message, $format);
        } else {
            $value = $message;
        }

        if (empty($vars)) {
            return $value;
        }

        foreach ($vars as $key => $var) {
            $value = preg_replace("/\%\{" . $key . "\}/", $var, $value);
        }

        return $value;
    }
}
