<?php

namespace Walrus\controllers;

use Walrus\core\WalrusFrontController;
use Walrus\core\WalrusFileManager;
use Exception;
use R;

/**
 * Class ConfigController
 * @package engine\controllers
 */
class ConfigController extends WalrusFrontController
{

    public function config()
    {
        if (isset($_POST['config'])) {
            if (!empty($_POST['RDBMS']) && !empty($_POST['hostname']) && !empty($_POST['databasename'])
                && !empty($_POST['user']) && !empty($_POST['url']) && !empty($_POST['templating'])
                && !empty($_POST['environment'])) {

                $filer = new WalrusFileManager(ROOT_PATH);

                $filer->setCurrentElem('Walrus/core/sample/config.sample');
                $config = $filer->getFileContent();
                $config = str_replace('%rdbms%', strtolower($_POST['RDBMS']), $config);
                $config = str_replace('%host%', $_POST['hostname'], $config);
                $config = str_replace('%database%', $_POST['databasename'], $config);
                $config = str_replace('%name%', $_POST['user'], $config);
                $config = str_replace('%password%', $_POST['password'], $config);
                $config = str_replace('%url%', $_POST['url'], $config);
                $config = str_replace('%templating%', strtolower($_POST['templating']), $config);
                $config = str_replace('%environment%', strtolower($_POST['environment']), $config);

                $filer->setCurrentElem('config');
                $filer->fileCreate('config.yml');

                $filer->setCurrentElem('config/config.yml');
                $filer->changeFileContent($config);

                $this->register('validation', true);
            } else {
                $this->register('validation', false);
            }
        } elseif (isset($_POST['check'])) {
            $response = array(
                'success' => false
            );
            if (!empty($_POST['RDBMS']) && !empty($_POST['hostname'])
                && !empty($_POST['databasename']) && !empty($_POST['user'])) {
                try {
                    R::setup(
                        $_POST['RDBMS'] . ':host=' . $_POST['hostname'] . ';dbname=' . $_POST['databasename'],
                        $_POST['user'],
                        $_POST['password']
                    );
                    R::debug(true);
                    $response['success'] = R::getDatabaseAdapter()->getDatabase()->isConnected();
                } catch (Exception $exception) {
                    $response['success'] = false;
                }
            }

            echo JSON_encode($response, true);
            return;
        }

        $this->register('post', isset($_POST) ? $_POST : false);
        $this->setView('config');
    }
}
