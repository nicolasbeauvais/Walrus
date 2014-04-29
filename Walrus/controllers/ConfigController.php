<?php

namespace Walrus\controllers;

use Walrus\core\WalrusCompile;
use Walrus\core\WalrusController;
use Walrus\core\WalrusFileManager;
use Exception;
use R;

/**
 * Class ConfigController
 * @package engine\controllers
 */
class ConfigController extends WalrusController
{

    public function config()
    {
        if (isset($_POST['config'])) {
            if (!empty($_POST['RDBMS']) && !empty($_POST['host']) && !empty($_POST['database'])
                && !empty($_POST['name']) && !empty($_POST['base_url']) && !empty($_POST['templating'])
                && !empty($_POST['environment'])) {

                $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH']);

                $config = WalrusCompile::newConfiguration($_POST);

                $filer->setCurrentElem('config');
                $filer->fileCreate('config.php');

                $filer->setCurrentElem('config/config.php');
                $filer->changeFileContent($config);

                $this->register('validation', true);
            } else {
                $this->register('validation', false);
            }
        } elseif (isset($_POST['check'])) {
            $response = array(
                'success' => false
            );
            if (!empty($_POST['RDBMS']) && !empty($_POST['host'])
                && !empty($_POST['database']) && !empty($_POST['name'])) {
                try {
                    R::setup(
                        $_POST['RDBMS'] . ':host=' . $_POST['host'] . ';dbname=' . $_POST['database'],
                        $_POST['name'],
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
