<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:59 27/01/14
 */

namespace Walrus\core;

use Walrus\core\entity\Skeleton;
use Walrus\core\entity\Template as Template;
use MtHaml;
use Spyc\Spyc;
use Exception;

/**
 * Class WalrusFrontController
 * @package Walrus\core
 */
class WalrusFrontController
{
    /**
     * All requested templated as a stack.
     * @var array
     */
    private static $templates = array();

    /**
     * 'Global' variables for all templates.
     * @var array
     */
    private static $variables = array();


    private static $foreach_key = '';
    private static $foreach_value = '';
    private static $foreach_key_lvl2 = '';
    private static $foreach_value_lvl2 = '';

    /**
     * Add a template to the stack.
     *
     * @param $view
     * @throws \Exception
     */
    protected function setView($view)
    {
        // @TODO: check config for templating
        $haml = new MtHaml\Environment('php');

        if (strrpos($view, '/') === false) {
            $className = explode('\\', get_called_class());
            $controller = strtolower(str_replace('Controller', '', end($className)));
            $template = FRONT_PATH . $controller . '/' . $view . '.haml';
        } else {
            $template = FRONT_PATH . $view . '.haml';
        }

        if (!file_exists($template)) {
            throw new Exception('[WalrusFrontController] requested template does not exist: ' . $template);
        }

        // @TODO: use WalrusFileManager
        $hamlCode = file_get_contents($template);

        if (!file_exists($template . '.php') || filemtime($template . '.php') != filemtime($template)) {

            $phpCode = $haml->compileString($hamlCode, $template);

            $tempnam = tempnam(dirname($template), basename($template));
            file_put_contents($tempnam, $phpCode);
            rename($tempnam, $template.'.php');
            touch($template.'.php', filemtime($template));
        }

        $objTemplate = new Template();
        $objTemplate->setName($view);
        $objTemplate->setTemplate($template . '.php');

        self::$templates[] = $objTemplate;
    }

    /**
     * Add variables to the specified template or to all if not specified.
     *
     * @param $key
     * @param $var
     * @param null $tpl
     * @throws \Exception
     */
    protected function register($key, $var, $tpl = null)
    {
        if (!isset($key) || !isset($var)) {
            throw new Exception('[WalrusFrontController] missing argument for function register');
        }

        if ($tpl) {
            foreach (self::$templates as $template) {
                if ($template->getName() === $tpl) {
                    $template->addVariable($key, $var);
                    return;
                }
            }
        } else {
            self::$variables[$key] = $var;
        }
    }

    /**
     * Display all templates in the stack order.
     */
    public static function execute()
    {
        if (count(self::$variables) > 0) {
            foreach (self::$variables as self::$foreach_key => self::$foreach_value) {
                ${self::$foreach_key} = self::$foreach_value;
            }
        }

        if (count(self::$templates) > 0) {
            foreach (self::$templates as self::$foreach_key => self::$foreach_value) {

                foreach (self::$foreach_value->getVariables() as self::$foreach_key_lvl2 => self::$foreach_value_lvl2) {
                    ${self::$foreach_key_lvl2} = self::$foreach_value_lvl2;
                }


                require(self::$foreach_value->getTemplate());

                foreach (self::$foreach_value->getVariables() as self::$foreach_key_lvl2 => self::$foreach_value_lvl2) {
                    unset(${self::$foreach_key_lvl2});
                }
            }
        }
    }

    /**
     * Set the use of a skeleton
     */
    public function skeleton()
    {
        //@TODO: create skeleton function
        /**
         * step 1: user create skeleton in yaml
         * step 2: yaml is parsed and stored || singleton on skeleton entity and parse the yaml only if needed
         * step 3: user choose a skeleton in is controller
         * step 4: user give variable to skeleton template's
         * step 5: display !
         *
         * step 6: lazy load handle :D
         */
    }
}
