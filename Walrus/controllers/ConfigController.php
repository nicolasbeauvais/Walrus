<?php

namespace Walrus\controllers;

use Walrus\core\WalrusFrontController;
use Walrus\core\WalrusFileManager;

/**
 * Class ConfigController
 * @package engine\controllers
 */
class ConfigController extends WalrusFrontController
{

    public function config()
    {
        if (isset($_POST['config'])) {
            if (!empty($_POST['RDBMS']) && !empty($_POST['host']) && !empty($_POST['database'])
                && !empty($_POST['name']) && !empty($_POST['templating']) && !empty($_POST['environment'])) {

                $filer = new WalrusFileManager(ROOT_PATH);

                $filer->setCurrentElem('Walrus/core/sample/config.sample');
                $config = $filer->getFileContent();
                $config = str_replace('%rdbms%', $_POST['RDBMS'], $config);
                $config = str_replace('%host%', $_POST['host'], $config);
                $config = str_replace('%database%', $_POST['database'], $config);
                $config = str_replace('%name%', $_POST['name'], $config);
                $config = str_replace('%password%', $_POST['password'], $config);
                $config = str_replace('%templating%', $_POST['templating'], $config);
                $config = str_replace('%environment%', $_POST['environment'], $config);

                $filer->setCurrentElem('config');
                $filer->fileCreate('config.yml');

                $filer->setCurrentElem('config/config.yml');
                $filer->changeFileContent($config);

                $this->register('validation', true);
            } else {
                $this->register('validation', false);
            }
        }

        $this->setView('config');
    }
}
