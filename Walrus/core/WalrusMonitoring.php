<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 20:27 24/02/14
 */

namespace Walrus\core;

use ReflectionClass;
use ReflectionMethod;

/**
 * Class WalrusMonitoring
 * @package Walrus\core
 */
class WalrusMonitoring
{

    // store all Exception and errors occured
    /**
     * @var array
     */
    private static $e2s = array();

    private static $executionTime = 0;

    /**
     * Contsructor for Monitoring
     */
    public function __construct()
    {
        set_exception_handler(array(&$this, 'exceptionHandler'));
        set_error_handler(array(&$this, 'errorHandler'));
        register_shutdown_function(array(&$this, 'monitoringExecute'));
    }

    public static function stop()
    {
        self::$executionTime = round(abs(microtime(true) - START_TIME) * 1000, 0);
    }

    /**
     * WalrusException format the exception and insert it in the log file.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $report = array(
            'type' => 'error',
            'title' => 'Error ' . $errno . ':',
            'content' => $errstr,
            'file' => substr($errfile, strlen(ROOT_PATH)),
            'real_path' => $errfile,
            'line' => $errline,
            'code' => $this->getCode(substr($errfile, strlen(ROOT_PATH)), $errline)
        );

        self::addE2s($report);
    }

    /**
     * Exception Handling.
     *
     * @param Exception|WalrusException $exception
     */
    public static function exceptionHandler($exception)
    {

        $traces = $exception->getTrace();

        $formatted_traces = array();
        foreach ($traces as $trace) {
            $trace['real_path'] = $trace['file'];
            $trace['file'] = substr($trace['file'], strlen(ROOT_PATH));
            $trace['code'] = self::getCode($trace['file'], $trace['line'], $trace['function']);
            $formatted_traces[] = $trace;
        }

        $report = array(
            'type' => 'exception',
            'title' => get_class($exception),
            'content' => $exception->getMessage(),
            'file' => substr($exception->getFile(), strlen(ROOT_PATH)),
            'code' => self::getCode(substr($exception->getFile(), strlen(ROOT_PATH)), $exception->getLine()),
            'real_path' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $formatted_traces
        );

        self::addE2s($report);
    }

    /**
     * @param WalrusException $exception
     */
    public static function exceptionCatcher(WalrusException $exception)
    {
        self::exceptionHandler($exception);
    }

    /**
     * Get semantic code block for the specified function/file/line
     *
     * @param string $file
     * @param int $line
     * @param null|string $function
     *
     * @return array
     */
    private static function getCode($file, $line, $function = null)
    {
        $filer = new WalrusFileManager(ROOT_PATH);
        $filer->setCurrentElem($file);

        if (isset($function) && class_exists(substr($file, 0, -4))) {

            $class = substr($file, 0, -4);

            $method = new ReflectionMethod($class, $function);
            $return['comment'] = $method->getDocComment();
            $start = $method->getStartLine() - 2;
            $end = $method->getEndLine() + 1;

            $return['highlight'] =  $line - $start - 1;
            $return['code'] = $filer->getFileContent(null, $start, $end);
        } elseif (class_exists(substr($file, 0, -4))) {
            $code = $filer->getFileContent('array');

            $i = $line;
            for ($i; $i > 0; $i--) {
                if (isset($code[$i])) {
                    preg_match('/\sfunction\s+([a-z_]\w+)/', $code[$i], $matches);
                    if (!empty($matches) && isset($matches[1])) {
                        $function = $matches[1];
                        break;
                    }
                }
            }

            $method = new ReflectionMethod(substr($file, 0, -4), $function);
            $return['comment'] = $method->getDocComment();
            $start = $method->getStartLine() - 2;
            $end = $method->getEndLine() + 1;

            $return['highlight'] =  $line - $i;
            $return['code'] = $filer->getFileContent(null, $start, $end);
        } else {
            $start = $line - 5;
            $end = $line;

            $return['highlight'] =  $line - $start - 1;
            $return['code'] = $filer->getFileContent(null, $start, $end);
        }
        $return['file'] = $file;

        return $return;
    }

    /**
     * Log Error and Exception into the log file
     */
    private function e2Process()
    {
        $filer = new WalrusFileManager(ROOT_PATH);
        if (!file_exists(ROOT_PATH . 'logs')) {
            $filer->folderCreate('logs');
        }
        if (!file_exists(ROOT_PATH . 'logs/error-exception-log.txt')) {
            $filer->setCurrentElem('logs');
            $filer->fileCreate('error-exception-log.txt');
        }
        $filer->setCurrentElem('logs/error-exception-log.txt');

        foreach (self::$e2s as $e2) {
            $rowDate = date('H:m:s d-M-Y');
            $rowType = ' [' . strtoupper($e2['type']) . ']';
            $rowFile = ' ' . $e2['file'] . ':' . $e2['line'];
            $rowMsg = ' | ' . $e2['content'];

            $row =  $rowDate . $rowType . $rowFile . $rowMsg . "\r\n";

            $filer->addFileContent($row);
        }
    }

    public function monitoringExecute()
    {
        $this->e2Process();

        self::stop();
        if ($_ENV['W']['environment'] == 'dev') {
            $e2s = self::$e2s;
            $e2nb = count(self::$e2s);
            $executionTime = self::$executionTime;

            if ($e2nb > 0) {
                require_once(ROOT_PATH . 'Walrus/templates/monitoring/e2.php');
            }

            require_once(ROOT_PATH . 'Walrus/templates/monitoring/toolbar.php');
        }
    }

    /**
     * Add a new Error or Exception to the e2s array.
     *
     * @param array $e2 contain a formatted Error or Exception
     */
    public static function addE2s($e2)
    {
        self::$e2s[] = $e2;
    }

    /**
     * @return array
     */
    public function getE2s()
    {
        return $this->e2s;
    }
}
