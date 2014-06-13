<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 19:09 10/06/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

/**
 * Class WalrusACL
 * @package Walrus\core
 */
class WalrusACL
{
    /**
     * Transform the ACL tree by a simple to use ACL array
     *
     * @param array $aclTree
     * @return array
     */
    public static function flattenACL($aclTree)
    {
        $roles = self::parseACL($aclTree);

        $flatten = array();

        foreach ($roles as $role => $data) {
            $flat = self::flatten($data);

            if (is_array($flat)) {
                $flatten[$role] = self::extractData($flat);
            } else {
                $flatten[$role] = $flat;
            }
        }

        return $flatten;
    }

    /**
     * Recursive: Parse ACL tree to extract roles
     *
     * @param array $aclTree
     * @param array $roles
     * @return array
     */
    private static function parseACL($aclTree, $roles = array())
    {
        foreach ($aclTree as $key => $value) {

            if (is_string($key)) {
                $roles[$key] = $value;

                if (is_array($value)) {
                    $roles = array_merge(self::parseACL($value, $roles), $roles);
                }
            }

        }

        return $roles;
    }

    /**
     * Recursive: Transform  a multidimensional array to a flat array
     *
     * @param array|string $data
     * @param array $flatArray
     * @return array
     */
    private static function flatten($data, $flatArray = array())
    {
        if (is_string($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $flatArray[$key] = null;
                $flatArray = array_merge(self::flatten($value, $flatArray), $flatArray);
            } else {
                $flatArray[$key] = $value;
            }
        }

        return $flatArray;
    }

    /**
     * Polish ACL array structure
     *
     * @param $data
     * @return array
     */
    public static function extractData($data)
    {

        $above = array();
        $tasks = array();

        foreach ($data as $key => $value) {

            if ($value !== null) {
                $tasks[] = $value;
            }

            if (is_string($key)) {
                $above[] = $key;
            }
        }

        return array(
            'above' => $above,
            'tasks' => $tasks
        );
    }

    /**
     * Return true if a user role can make a task
     *
     * @param string $userRole
     * @param string $task
     * @return bool
     *
     * @throws WalrusException if the user role doesn't exist
     */
    public static function hasRight($userRole, $task)
    {
        if (!isset($_ENV['W']['acls'][$userRole])) {
            throw new WalrusException('The "' . $userRole . '" role doesn\'t exist');
        }

        return in_array($task, $_ENV['W']['acls'][$userRole]['tasks']);
    }

    /**
     * Return true if a user role is above the specified role
     *
     * @param string $userRole
     * @param string $role
     * @return bool
     *
     * @throws WalrusException if the user role doesn't exist
     */
    public static function hasRole($userRole, $role)
    {
        if (!isset($_ENV['W']['acls'][$userRole])) {
            throw new WalrusException('The "' . $userRole . '" role doesn\'t exist');
        }

        return $userRole == $role || in_array($role, $_ENV['W']['acls'][$userRole]['above']);
    }
}
