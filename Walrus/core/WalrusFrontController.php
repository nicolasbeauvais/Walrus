<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 16:59 27/01/14
 */

namespace Walrus\core;

use MtHaml;

class WalrusFrontController
{
    private $template = '';

    private $registered = array();

    protected function setView($view)
    {
        // check config for templating
        $haml = new MtHaml\Environment('php');

        $template = FRONT_PATH . 'hello/home.haml';
        $hamlCode = file_get_contents($template);

        if (!file_exists($template . '.php') || filemtime($template . '.php') != filemtime($template)) {

            $phpCode = $haml->compileString($hamlCode, $template);

            $tempnam = tempnam(dirname($template), basename($template));
            file_put_contents($tempnam, $phpCode);
            rename($tempnam, $template.'.php');
            touch($template.'.php', filemtime($template));
        }

        $this->template = $template . '.php';
        $this->execute();
    }

    protected function register($key, $var)
    {
        if (!isset($key) || !isset($var)) {
            throw new Exception('[WalrusFrontController] missing argument for function register');
        }

        $this->registered[$key] = $var;
    }

    private function execute()
    {
        if (count($this->registered) > 0) {
            foreach ($this->registered as $WALRUS_key => $WALRUS_value) {
                ${$WALRUS_key} = $WALRUS_value;
            }
            unset($WALRUS_key);
            unset($WALRUS_value);
        }

        require($this->template);
    }
}
