<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 00:09 08/02/14
 */

namespace engine\api;

use Walrus\core\WalrusAPI;

/**
 * Class PollingController
 * @package engine\api
 */
class PollingController extends WalrusAPI
{
    /**
     * @return array
     */
    public function run()
    {
        return self::setPolling(array('posts', 'users'), array(&$this, 'processPolling'));
    }

    public function processPolling()
    {
        $posts = $this->model('post')->getLast(self::$last_ids['posts']);

        if (!empty($posts)) {
            $response['posts'] = $posts;
            self::$last_ids['posts'] = end($posts)['id'];
            return $response;
        }
    }
}
