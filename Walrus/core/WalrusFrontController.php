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


    /**
     * Contain skeleton if they have been requested
     * @var string
     */
    private static $skeletons = array();

    /**
     * @var mixed
     */
    private static $foreach_key;
    /**
     * @var mixed
     */
    private static $foreach_value;
    /**
     * @var mixed
     */
    private static $foreach_key_lvl2;
    /**
     * @var mixed
     */
    private static $foreach_value_lvl2;
    /**
     * @var mixed
     */
    private static $foreach_skeleton_key;
    /**
     * @var mixed
     */
    private static $foreach_skeleton_value;
    /**
     * @var mixed
     */
    private static $foreach_skeleton_key_lvl2;
    /**
     * @var mixed
     */
    private static $foreach_skeleton_value_lvl2;

    /**
     * Add a template to the stack.
     *
     * @param $view
     * @throws \Exception
     */
    protected function setView($view, $alias = null)
    {
        if (strrpos($view, '/') === false) {
            $className = explode('\\', get_called_class());
            $controller = strtolower(str_replace('Controller', '', end($className)));
            $template = FRONT_PATH . $controller . '/' . $view . '.haml';
        } else {
            $template = FRONT_PATH . $view . '.haml';
        }


        $objTemplate = new Template();
        $objTemplate->setName($view);
        if ($alias) {
            $objTemplate->setAlias($alias);
        }
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
    protected function register($key, $var, $tpl = null, $alias = null, $skeletonName = null)
    {
        if (!isset($key) || !isset($var)) {
            throw new Exception('[WalrusFrontController] missing argument for function register');
        }

        if ($skeletonName) {
            foreach (self::$skeletons as $skeleton) {
                if ($skeleton->getName() === $skeletonName) {
                    foreach ($skeleton->getTemplates() as $template) {
                        if (($tpl && $alias && $template->getName() === $tpl
                            && $alias && $template->getAlias() === $alias)
                            || ($tpl && !$alias && $template->getName() === $tpl)
                            || (!$tpl && ($alias && $template->getAlias() === $alias))) {
                            $template->addVariable($key, $var);
                        }
                    }
                }
            }
        } elseif ($tpl) {
            foreach (self::$templates as $template) {
                if ($template->getName() === $tpl) {
                    if (!$alias || ($alias && $template->getAlias() === $alias)) {
                        $template->addVariable($key, $var);
                    }
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
                if (is_a(self::$foreach_value, 'Walrus\core\entity\Skeleton')) {
                    self::process(self::$foreach_value);
                } else {

                    foreach (self::$foreach_value->getVariables() as
                             self::$foreach_key_lvl2 => self::$foreach_value_lvl2) {
                        ${self::$foreach_key_lvl2} = self::$foreach_value_lvl2;
                    }

                    // @TODO: check config for templating
                    self::compileToYaml(substr(self::$foreach_value->getTemplate(), 0, -4));

                    require(self::$foreach_value->getTemplate());

                    foreach (self::$foreach_value->getVariables() as
                             self::$foreach_key_lvl2 => self::$foreach_value_lvl2) {
                        unset(${self::$foreach_key_lvl2});
                    }
                }
            }
        }
    }

    /**
     * child process for execute
     */
    private static function process()
    {
        foreach (self::$foreach_value->getTemplates() as self::$foreach_skeleton_key => self::$foreach_skeleton_value) {

            foreach (self::$foreach_skeleton_value->getVariables() as
                     self::$foreach_skeleton_key_lvl2 => self::$foreach_skeleton_value_lvl2) {
                ${self::$foreach_skeleton_key_lvl2} = self::$foreach_skeleton_value_lvl2;
            }

            // @TODO: check config for templating
            self::compileToYaml(substr(self::$foreach_skeleton_value->getTemplate(), 0, -4));

            require(self::$foreach_skeleton_value->getTemplate());

            foreach (self::$foreach_skeleton_value->getVariables() as
                     self::$foreach_skeleton_key_lvl2 => self::$foreach_skeleton_value_lvl2) {
                unset(${self::$foreach_skeleton_key_lvl2});
            }
        }
    }


    /**
     * Set the use of a skeleton
     */
    public function skeleton($name)
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
