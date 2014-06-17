<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 08:07 04/03/14
 */
 

namespace Walrus\core;

use Exception;
use Walrus\core\WalrusMonitoring;

/**
 * Class WalrusException
 * @package Walrus\core\objects
 */
class WalrusException extends Exception
{
    /**
     * Exception constructor
     */
    public function __construct($name)
    {
        $trace = debug_backtrace();

        if (isset($trace[1]) && isset($trace[1]['class'])) {
            if (strpos('\\', $trace[1]['class']) != -1) {
                $namespace = explode('\\', $trace[1]['class']);
                $prefix = end($namespace);
            } else {
                $prefix = $trace[1]['class'];
            }

            $name = $prefix . ': ' . $name;
        }

        $this->message = $name;
    }

    /**
     * Handle a WalrusException to display it in the exception debuger
     */
    public function handle()
    {
        WalrusMonitoring::exceptionCatcher($this);
    }
}
