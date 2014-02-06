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
      if ($_POST['config']) {
	if (empty($_POST['dbName']) || empty($_POST['dbHost']) || empty($_POST['dbUser']) || empty($_POST['templating']) || empty($_POST['environment']) || empty($_POST['dbLanguage'])) {
	  $this->setView('config');
	  $error = "You didn't feed the information correctly. Please fix it."
	}
	else {
	  $configInfo = $_POST;
	  //appel a la fonction qui feed la config. avec en parametre $configInfo
	}
      }
      else
        $this->setView('config');
    }
}
