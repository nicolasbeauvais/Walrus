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
    protected function setView($view)
    {
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

        require($template . '.php');
    }
}
