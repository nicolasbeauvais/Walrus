<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 07:55 10/03/14
 */
 

namespace Test;

use PHPUnit_Framework_TestCase;
use Walrus\core\WalrusAutoload;

/**
 * Class WalrusAutoloadTest
 * @package Test
 */
class WalrusAutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testGetNamespace ()
    {
        $namespace = WalrusAutoload::getNamespace('WalrusFileManager');
        $this->assertEquals('Walrus\core\WalrusFileManager', $namespace);

        $namespace = WalrusAutoload::getNamespace('Route');
        $this->assertEquals('Walrus\core\objects\Route', $namespace);

        $namespace = WalrusAutoload::getNamespace('WalrusKernel');
        $this->assertEquals('Walrus\core\WalrusKernel', $namespace);

        $namespace = WalrusAutoload::getNamespace('ConfigController');
        $this->assertEquals('Walrus\controllers\ConfigController', $namespace);

        $namespace = WalrusAutoload::getNamespace('HelloController');
        $this->assertEquals('engine\controllers\HelloController', $namespace);
    }
}
