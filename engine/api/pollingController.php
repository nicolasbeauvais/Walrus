<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 00:09 08/02/14
 */

namespace engine\api;

use Walrus\core\objects\SessionHandler;
use Walrus\core\WalrusAPI;

/**
 * Class PollingController
 * @package engine\api
 */
class PollingController extends WalrusAPI
{
    public function run()
    {
        $session_handler = new SessionHandler();
        session_set_save_handler($session_handler);
        session_start();

        $start = time();
        $longPollingCycleTime = 5;
        $realTimeLatency = 1;

        session_id();
        session_write_close();

        $response = array();

        while (time() < $start + $longPollingCycleTime) {


            if (!empty($msgs)) {
                exit();
            }
            sleep($realTimeLatency);
        }

        $response['msgs'] = array();
        return $response;
    }
}
