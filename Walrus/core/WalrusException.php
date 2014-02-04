<?php

/**
 * Walrus Framework
 * File maintened by: Guillaume Flambard
 * Created: 20:46 15/01/14
 */

namespace Walrus\core;

use Exception;

/**
* WalrusException format the exception and insert in the log file
* Walrus/log.txt
* In this format :
* 14:26:54 30/01/2014 | [line] -> 29 | [class] -> WalrusException | [Error] -> "Dat fake error"
*/

/**
* If you want use this class :
*
* try
* {
*	throw new WalrusException("Dat fake error");
* }
* catch(WalrusException $e)
* {
*	$error = new WalrusException;
*	$error->MyError($e);
* }
*/

class WalrusException extends Exception
{
	function MyError($e)
	{
		$errorInfo="\n".date('H:i:s d/m/Y')." | [line] -> ".$e->line." | [class] -> ".str_replace('.php','',basename($e->file))." | [Error] -> \"".$e->message."\"";
		$logPath = __DIR__."/../../log.txt";
		file_put_contents($logPath, $errorInfo, FILE_APPEND | LOCK_EX); // add the argument in the file but after with file append
	}
}



?>