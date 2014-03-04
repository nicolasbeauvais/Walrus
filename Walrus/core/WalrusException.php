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
     * Handle a WalrusException to display it in the exception debuger
     */
    public function handle()
    {
        WalrusMonitoring::exceptionCatcher($this);
    }
}
