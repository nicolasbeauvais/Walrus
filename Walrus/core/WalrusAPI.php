<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 00:29 08/02/14
 */

namespace Walrus\core;

use Walrus\core\objects\SessionHandler;
use Walrus\core\WalrusException;

/**
 * Class WalrusAPI
 * @package Walrus\core
 */
class WalrusAPI
{
    /**
     * @var array
     */
    private static $allow = array();

    /**
     * @var string
     */
    private static $contentType = "application/json";

    /**
     * @var array
     */
    private static $request = array();

    /**
     * @var int
     */
    private static $code = 200;

    /**
     * Array of instancied controllers
     */
    private $models;

    /**
     * Array of instancied controllers
     */
    private $controllers;

    /**
     * Register table to parse for polling
     */
    private static $polling;

    /**
     * Contain the last id of all table from polling
     */
    public static $last_ids = array();

    /**
     * Init an API process
     */
    public static function init()
    {
        self::inputs();
    }

    /**
     * Display API process result
     * @param $data API data
     */
    public static function execute($data)
    {
        self::response($data, self::$code);
    }

    /**
     * Return an instance of the specified controller
     *
     * @param string $controller
     *
     * @return Class the specofied controller class
     * @throws WalrusException if the controller doesn't exist
     */
    protected function controller($controller)
    {
        $controllerClass = ucwords(strtolower($controller)) . 'Controller';

        if (isset($this->controllers[$controllerClass])) {
            return $this->controllers[$controllerClass];
        }

        $controllerClassWithNamespace =  WalrusAutoload::getNamespace($controllerClass);

        if (!$controllerClassWithNamespace) {
            throw new WalrusException('[WalrusController] request unexistant controller: ' . $controllerClass);
        }

        $controllerInstance = new $controllerClassWithNamespace();
        $this->controllers[$controllerClass] = $controllerInstance;

        return $controllerInstance;
    }

    /**
     * Return an instance of the specified model
     *
     * @param string $model
     *
     * @throws WalrusException if the model doesn't exist
     * @return Class the specified model class
     */
    protected function model($model)
    {
        $modelClass = ucwords(strtolower($model));

        if (isset($this->models[$modelClass])) {
            return $this->models[$modelClass];
        }

        $modelClassWithNamespace =  WalrusAutoload::getNamespace($modelClass);

        if (!$modelClassWithNamespace) {
            throw new WalrusException('[WalrusController] request unexistant model: ' . $modelClass);
        }

        $modelInstance = new $modelClassWithNamespace();
        $this->models[$modelClass] = $modelInstance;

        return $modelInstance;
    }

    /**
     * @return mixed
     */
    public function getReferer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * @param $data
     * @param $status
     */
    private static function response($data, $status)
    {
        self::$code = ($status) ? $status : 200;
        self::setHeaders();

        $result = array(
            'status' => self::$code,
            'data' => $data
        );

        echo JSON_encode($result);
        exit;
    }

    /**
     * @return mixed
     */
    private static function getStatusMessage()
    {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[self::$code]) ? $status[self::$code] : $status[500];
    }

    /**
     * @return mixed
     */
    private static function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     *
     */
    private static function inputs()
    {
        switch(self::getRequestMethod()){
            case "POST":
                self::$request = self::cleanInputs($_POST);
                break;
            case "GET":
            case "DELETE":
                self::$request = self::cleanInputs($_GET);
                break;
            case "PUT":
                parse_str(file_get_contents("php://input"), self::$request);
                self::$request = self::cleanInputs(self::$request);
                break;
            default:
                self::response('', 406);
                break;
        }
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    private static function cleanInputs($data)
    {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = self::cleanInputs($v);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            $clean_input = trim($data);
        }
        return $clean_input;
    }

    /**
     *
     */
    private static function setHeaders()
    {
        header("HTTP/1.1 " . self::$code . " " . self::getStatusmessage());
        header("Content-Type:" . self::$contentType);
    }

    /**
     * Change the http status code
     * @param int $code
     */
    public static function setCode($code)
    {
        self::$code = $code;
    }

    /**
     * @return array
     */
    public static function getPolling()
    {
        return self::$polling;
    }

    /**
     * @param array $polling
     * @param array $callback
     * @param int $longPollingCycleTime
     * @param int $realTimeLatency
     *
     * @return result
     */
    public static function setPolling($polling, $callback, $longPollingCycleTime = 10, $realTimeLatency = 1)
    {
        self::$polling = $polling;

        $session_handler = new SessionHandler();
        session_set_save_handler($session_handler);
        session_start();

        $start = time();

        session_id();
        session_write_close();

        $response = array();
        $ids_temp = self::$last_ids;

        while (time() < $start + $longPollingCycleTime) {

            $response = call_user_func($callback);

            if (empty($response)) {
                sleep($realTimeLatency);
            } else {
                break;
            }
        }

        $ids_end = self::$last_ids;

        if ($ids_modified = array_diff($ids_end, $ids_temp)) {
            $session_handler->save($ids_modified);
        }
        return $response;
    }
}
