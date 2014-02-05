<?php

namespace Walrus\controllers;

use Walrus\core\WalrusFrontController as WalrusFrontController;

/**
 * Class ConfigController
 * @package engine\controllers
 */
class ConfigController extends WalrusFrontController
{

    public function config()
    {
        $this->setView('config');
    }
}
