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

    /**
     * Contsructor for Monitoring
     */
    public function __construct()
    {
        set_exception_handler(array(&$this, 'exceptionHandler'));
        set_error_handler(array(&$this, 'errorHandler'));
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
            'title' => 'Error ' . $errno . ':',
            'content' => $errstr,
            'file' => $errfile,
            'line' => $errline
        );

        $this->e2Execute('error', $report);
    }

    /**
     * Exception Handling.
     *
     * @param Exception $exception
     */
    public function exceptionHandler(Exception $exception)
    {
        $report = array(
            'title' => 'Exception:',
            'content' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        );

        $this->e2Execute('exception', $report);
    }

    /**
     * Process for error & exception
     */
    private function e2Execute($type, $report)
    {
        if ($_ENV['W']['environment'] == 'dev') {
            echo '<table>
                    <tr>
                      <td>' . $report['title'] . '</td>
                      <td>' . $report['content'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $report['line'] . '</td>
                      <td>' . $report['file'] . '</td>
                    </tr>';
            if (isset($report['trace'])) {
                foreach ($report['trace'] as $trace) {
                    echo '<table>
                            <tr>
                              <td>' . $trace['title'] . '</td>
                              <td>' . $trace['content'] . '</td>
                            </tr>
                            <tr>
                              <td>' . $trace['line'] . '</td>
                              <td>' . $trace['file'] . '</td>
                            </tr>
                            <tr>
                              <td>' . $trace['class'] . '</td>
                              <td>' . $trace['function'] . '</td>
                            </tr>
                          </table>';
                }
            }
            echo '</table>';
        }
    }
}
