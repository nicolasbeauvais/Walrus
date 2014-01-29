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

        if (count(self::$skeletons) === 0) {
            $this->loadSkeletons();
        }

        foreach (self::$skeletons as $skeleton) {
            if ($skeleton->getName() === $name) {
                self::$templates[] = $skeleton;
                break;
            }
        }
    }

    /**
     * Load skeletons from skeleton.yml
     */
    private function loadSkeletons()
    {
        $skeleton_yaml = "../config/skeleton.yml";

        if (!file_exists($skeleton_yaml)) {
            throw new Exception('[WalrusFrontController] skeleton.yml doesn\'t exist in config/ directory');
        }

        $skeletons = Spyc::YAMLLoad($skeleton_yaml);

        foreach ($skeletons as $skeletonName => $skeleton) {

            $templates = array();

            foreach ($skeleton as $name => $value) {

                $template = FRONT_PATH . $value['template'] . '.haml';

                $objTemplate = new Template();
                $objTemplate->setName($name);
                $objTemplate->setTemplate($template . '.php');
                if (isset($value['alias'])) {
                    $objTemplate->setAlias($value['alias']);
                }

                $templates[] = $objTemplate;
            }

            $objSkeleton = new Skeleton();
            $objSkeleton->setName($skeletonName);
            $objSkeleton->setTemplate($templates);

            self::$skeletons[] = $objSkeleton;
        }
    }

    /**
     * Compile yaml file
     */
    private static function compileToYaml ($template)
    {
        $haml = new MtHaml\Environment('php');

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
    }
}
