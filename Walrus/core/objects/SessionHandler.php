<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 14:20 08/02/14
 */

namespace Walrus\core\objects;

use R;
use SessionHandlerInterface;
use Walrus\core\WalrusAPI;

/**
 * Class SessionHandler
 * @package Walrus\core\objects
 */
class SessionHandler implements SessionHandlerInterface
{
    private $id;

    /**
     * @param $savePath
     * @param $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        //initialisation du gestionnaire de sessions
        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        //fermeture / destruction du gestionnaire de sessions
        return true;
    }

    /**
     * @param $session_id
     *
     * @return bool
     */
    public function read($session_id)
    {
        $sessionData = R::findOne(
            'session_data',
            'session_id = :session_id',
            array(':session_id' => $session_id)
        );

        if (empty($data)) {
            return false;
        } else {
            return $sessionData;
        }
    }

    /**
     * @param $session_id
     * @param $data
     *
     * @return mixed
     */
    public function write($session_id, $data)
    {
        $expire = intval(time() + 7200);

        $session = R::findOne(
            'sessions',
            'session_id = :session_id',
            array(':session_id' => $session_id)
        );

        if (!$session) {
            $tables = WalrusAPI::getPolling();
            $last_ids = array();
            foreach ($tables as $table) {
                $bean = R::findLast($table);
                $last_ids[$table] = $bean->id;
            }
            WalrusAPI::$last_ids = $last_ids;

            $sessions = R::dispense('sessions');
            $sessions->session_id = $session_id;
            $sessions->session_expire = $expire;
            $sessions->session_data = json_encode($last_ids);

            $this->id = $sessions->id;
            R::store($sessions);
        } else {
            $sessions = R::load('sessions', $session->id);
            $sessions->session_expire = $expire;

            $this->id = $sessions->id;
            WalrusAPI::$last_ids = json_decode($sessions->session_data, true);

            R::store($sessions);
        }

        return true;
    }

    /**
     * @param $session_id
     *
     * @return mixed
     */
    public function destroy($session_id)
    {
        $sessions = R::load('sessions', $session_id);
        R::trash($sessions);

        return true;
    }

    /**
     * @param $maxlifetime
     *
     * @return mixed
     */
    public function gc($maxlifetime)
    {
        $sessions = R::find(
            'sessions',
            'WHERE session_expire < :time',
            array(':time' => time())
        );

        R::trashAll($sessions);

        return true;
    }

    /**
     * @param $ids
     */
    public function save($ids)
    {
        foreach ($ids as $key => $id) {
            $sessions = R::load('sessions', $this->id);
            $data = json_decode($sessions->session_data, true);
            $data[$key] = $id;
            $sessions->session_data = json_encode($data);
            R::store($sessions);
        }
    }
}
