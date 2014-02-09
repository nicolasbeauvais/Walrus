<?php

/**
 * Walrus Framework
 * File maintened by: Guillaume Flambard
 * Created: 20:46 15/01/14
 */

namespace Walrus\core;

use Exception;


/**
 * Class WalrusException
 * @package Walrus\core
 */
class WalrusException extends Exception
{
    // @TODO: refactor

    /**
     * WalrusException format the exception and insert it in the log file.
     */
    public function errorHandler()
    {
        $errorInfo= "\n" . date('H:i:s d/m/Y')." | [line] -> " . $this->line . " | [class] -> "
            . str_replace('.php', '', basename($this->file)) . " | [Error] -> \"" . $this->message . "\"";
        $logPath = __DIR__."/../../log.txt";
        file_put_contents($logPath, $errorInfo, FILE_APPEND | LOCK_EX);
    }
}
