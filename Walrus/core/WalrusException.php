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
	function MyError()
	{
		$errorInfo="\n".date('H:i:s d/m/Y')." | [line] -> ".$this->line." | [class] -> ".str_replace('.php','',basename($this->file))." | [Error] -> \"".$this->message."\"";
		$logPath = __DIR__."/../../log.txt";
		file_put_contents($logPath, $errorInfo, FILE_APPEND | LOCK_EX); // add the argument in the file but after with file append
	}
}
