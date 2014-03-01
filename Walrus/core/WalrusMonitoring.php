<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 20:27 24/02/14
 */

namespace Walrus\core;

/**
 * Class WalrusMonitoring
 * @package Walrus\core
 */
class WalrusMonitoring
{
    // @TODO: need a static method

    // store all Exception and errors occured
    private $e2s = array();

    /**
     * Contsructor for Monitoring
     */
    public function __construct()
    {
        set_exception_handler(array(&$this, 'exceptionHandler'));
        set_error_handler(array(&$this, 'errorHandler'));
        register_shutdown_function(array(&$this, 'monitoringExecute'));
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
            'line' => $errline
        );

        $this->addE2s($report);
    }

    /**
     * Exception Handling.
     *
     * @param Exception $exception
     */
    public function exceptionHandler($exception)
    {
        $traces = $exception->getTrace();

        $formatted_traces = array();
        foreach ($traces as $trace) {
            $trace['real_path'] = $trace['file'];
            $trace['file'] = substr($trace['file'], strlen(ROOT_PATH));
            $formatted_traces[] = $trace;
        }

        $report = array(
            'type' => 'exception',
            'title' => get_class($exception),
            'content' => $exception->getMessage(),
            'file' => substr($exception->getFile(), strlen(ROOT_PATH)),
            'real_path' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $formatted_traces
        );

        $this->addE2s($report);
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

        foreach ($this->e2s as $e2) {
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

        if ($_ENV['W']['environment'] == 'dev') {
            $e2s = $this->e2s;
            $e2nb = count($this->e2s);

            require_once(ROOT_PATH . 'Walrus/templates/monitoring/toolbar.php');
        }
    }

    /**
     * Add a new Error or Exception to the e2s array.
     *
     * @param array $e2 contain a formatted Error or Exception
     */
    public function addE2s($e2)
    {
        $this->e2s[] = $e2;
    }

    /**
     * @return array
     */
    public function getE2s()
    {
        return $this->e2s;
    }
}