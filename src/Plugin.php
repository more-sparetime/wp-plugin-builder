<?php

namespace MoreSparetime\WordPress\PluginBuilder;

use AndreasGlaser\Helpers\ArrayHelper;
use AndreasGlaser\Helpers\Validate\Expect;
use AndreasGlaser\Helpers\View\PHPView;
use MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu;
use MoreSparetime\WordPress\PluginBuilder\Ajax\Ajax;
use MoreSparetime\WordPress\PluginBuilder\Controller\ControllerInterface;
use MoreSparetime\WordPress\PluginBuilder\Cron\Cron;
use MoreSparetime\WordPress\PluginBuilder\Shortcode\Shortcode;

/**
 * Class Plugin
 *
 * @package MoreSparetime\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
class Plugin implements PluginInterface, AttachableInterface
{
    /**
     * @var string
     */
    protected $slug = 'my_plugin';

    /**
     * @var string
     */
    protected $slugSeparator = '_';

    /**
     * @var array
     */
    protected $models = [];

    /**
     * @var \MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu[]
     */
    protected $menus = [];

    /**
     * @var Shortcode[]|array
     */
    protected $shortcodes = [];

    /**
     * @var Ajax[]
     */
    protected $ajaxCalls = [];

    /**
     * @var Cron[]
     */
    protected $crons = [];

    /**
     * @var callable[]
     */
    protected $activationCallbacks = [];

    /**
     * @var callable[]
     */
    protected $deactivationCallbacks = [];

    /**
     * @var callable[]
     */
    protected $uninstallCallbacks = [];

    /**
     * @var bool
     */
    protected $translate = false;

    /**
     * @var array
     */
    protected $controllers = [];

    /**
     * @var array
     */
    protected $config = [
        'views_dir' => null,
    ];

    /**
     * @var array
     */
    protected $filters =[];

    /**
     * @var array
     */
    protected $javascripts = [];

    /**
     * @var array|
     */
    protected $stylesheets = [];

    public function __construct($prefix, array $config = [])
    {
        $this->slug = $prefix;
        $this->config = ArrayHelper::merge($this->config, $config);

        if ($this->config['views_dir']) {
            if (!is_dir($this->config['views_dir'])) {
                throw new \Exception(sprintf('"%s" is not a directory', $this->config['views_dir']));
            }

            PHPView::setGlobal('plugin', $this);
        }

        $this->initModels();
    }

    /**
     * @author Xavier Sanna
     */
    private function initModels()
    {
        $path = $this->getPluginPath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Features' . DIRECTORY_SEPARATOR . '*';

        foreach(glob($path , GLOB_ONLYDIR) AS $directory) {
            $namespace = '\Plugin\Features\\' . basename($directory) . '\\' . 'Models' . '\\';

            $subPath = $directory . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR .'*';

            foreach(glob($subPath) AS $model) {
                $name = $namespace . str_replace('.php', '',basename($model));

                array_push($this->models, new $name());
            }
        }
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getSlugSeparator()
    {
        return $this->slugSeparator;
    }

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu $menu
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addMenu(Menu $menu)
    {
        if (!in_array($menu, $this->menus)) {
            $this->menus[] = $menu;
        }

        return $this;
    }

    /**
     * Shorthand
     *
     * @param string   $slug
     * @param callable $controller
     * @param string   $recurrence
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function cron($slug, $controller, $recurrence = 'hourly')
    {
        Expect::str($slug);
        Expect::isCallable($controller);
        Expect::str($recurrence);

        return $this->addCron(new Cron($this, $slug, $controller, $recurrence));
    }

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Cron\Cron $cron
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addCron(Cron $cron)
    {
        if (!in_array($cron, $this->crons)) {
            $this->crons [] = $cron;
        }

        return $this;
    }

    /**
     * @param string   $slug
     * @param callable $controller
     * @param array    $context
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function shortcode($slug, $controller, array $context = [])
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        return $this->addShortcode(new Shortcode($this, $slug, $controller, $context));
    }

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Shortcode\Shortcode $shortcode
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addShortcode(Shortcode $shortcode)
    {
        if (!in_array($shortcode, $this->shortcodes)) {
            $this->shortcodes [] = $shortcode;
        }

        return $this;
    }

    /**
     * Helper for adding ajax calls.
     *
     * @param string   $slug
     * @param callable $controller
     * @param bool     $internal
     * @param bool     $external
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCall($slug, $controller, $internal = true, $external = false)
    {
        Expect::str($slug);
        Expect::isCallable($controller);
        Expect::bool($internal);
        Expect::bool($external);

        return $this->addAjaxCall(new Ajax($this, $slug, $controller, $internal, $external));
    }

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Ajax\Ajax $ajax
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addAjaxCall(Ajax $ajax)
    {
        if (!in_array($ajax, $this->ajaxCalls)) {
            $this->ajaxCalls[] = $ajax;
        }

        return $this;
    }

    /**
     * Adds ajax call, available for admin pages.
     *
     * @param $slug
     * @param $controller
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallInternal($slug, $controller)
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        return $this->addAjaxCall(new Ajax($this, $slug, $controller, true, false));
    }

    /**
     * Adds ajax call available for external user front-end.
     *
     * @param $slug
     * @param $controller
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallExternal($slug, $controller)
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        return $this->addAjaxCall(new Ajax($this, $slug, $controller, false, true));
    }

    /**
     * Adds ajax call available for both admin pages and external user front-end.
     *
     * @param $slug
     * @param $controller
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallGlobal($slug, $controller)
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        return $this->addAjaxCall(new Ajax($this, $slug, $controller, true, true));
    }

    /**
     * @param $controller
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addActivationCallback($controller)
    {
        Expect::isCallable($controller);

        if (!in_array($controller, $this->activationCallbacks)) {
            $this->activationCallbacks[] = $controller;
        }

        return $this;
    }

    /**
     * @param $controller
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addDeactivationCallback($controller)
    {
        Expect::isCallable($controller);

        if (!in_array($controller, $this->deactivationCallbacks)) {
            $this->deactivationCallbacks[] = $controller;
        }

        return $this;
    }

    /**
     * @param $controller
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addUninstallCallback($controller)
    {
        Expect::isCallable($controller);

        if (!in_array($controller, $this->uninstallCallbacks)) {
            $this->uninstallCallbacks[] = $controller;
        }

        return $this;
    }

    /**
     * @param string      $scriptAlias
     * @param null|string $src
     * @param array       $deps
     * @param bool        $ver
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addJsHeader($scriptAlias, $src = null, $deps = [], $ver = false)
    {
        Expect::str($scriptAlias);

        if ($src) {
            Expect::str($src);
        }

        $this->javascripts[$scriptAlias] = [
            'src'       => $src,
            'deps'      => $deps,
            'ver'       => $ver,
            'in_footer' => false,
        ];

        return $this;
    }

    /**
     * @param string      $scriptAlias
     * @param null|string $src
     * @param array       $deps
     * @param bool        $ver
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addJsFooter($scriptAlias, $src = null, $deps = [], $ver = false)
    {
        Expect::str($scriptAlias);

        if ($src) {
            Expect::str($src);
        }

        $this->javascripts[$scriptAlias] = [
            'src'       => $src,
            'deps'      => $deps,
            'ver'       => $ver,
            'in_footer' => true,
        ];

        return $this;
    }

    /**
     * @param string $styleAlias
     * @param string $src
     * @param array  $deps
     * @param bool   $ver
     * @param string $media
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addCss($styleAlias, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        Expect::str($styleAlias);

        if ($src) {
            Expect::str($src);
        }

        Expect::arr($deps);
        Expect::str($media);

        $this->stylesheets[$styleAlias] = [
            'src'   => $src,
            'deps'  => $deps,
            'ver'   => $ver,
            'media' => $media,
        ];

        return $this;
    }

    public function addWidget($name)
    {
        add_action( 'widgets_init', function() use ($name){
            register_widget( '\Plugin\Widgets\\'. $name );
        });
    }

    /**
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        if ($this->translate) {
            add_action('plugins_loaded', function () {
                load_plugin_textdomain(
                    $this->getSlug(),
                    false,
                    $this->getSlug() . '/assets/languages/'
                );
            });
        }

        if (!defined('DOING_AJAX')) {
            if (is_admin()) {
                $filePath = $this->getPluginFilePath();

                foreach ($this->activationCallbacks AS $activationCallback) {
                    register_activation_hook($filePath, $activationCallback);
                }

                foreach ($this->deactivationCallbacks AS $deactivationCallback) {
                    register_deactivation_hook($filePath, $deactivationCallback);
                }

                foreach ($this->uninstallCallbacks AS $uninstallCallback) {
                    register_uninstall_hook($filePath, $uninstallCallback);
                }

            }

            foreach ($this->menus AS $menu) {
                if ($menu instanceof AttachableInterface) {
                    $menu->attachHooks();
                }
            }

            foreach ($this->shortcodes AS $shortcode) {
                if ($shortcode instanceof AttachableInterface) {
                    $shortcode->attachHooks();
                }
            }

            foreach ($this->javascripts AS $scriptAlias => $javascript) {
                wp_enqueue_script($scriptAlias, $javascript['src'], $javascript['deps'], $javascript['ver'], $javascript['in_footer']);
            }

            foreach ($this->stylesheets AS $styleAlias => $stylesheet) {
                wp_enqueue_script($styleAlias, $stylesheet['src'], $stylesheet['deps'], $stylesheet['ver']);
            }
        }

        foreach ($this->ajaxCalls AS $ajaxCall) {
            if ($ajaxCall instanceof AttachableInterface) {
                $ajaxCall->attachHooks();
            }
        }

        foreach ($this->crons AS $cron) {
            if ($cron instanceof AttachableInterface) {
                $cron->attachHooks();
            }
        }
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Returns absolute path to plugin root file.
     *
     * @return string
     * @author Andreas Glaser
     */
    public function getPluginFilePath()
    {
        return $this->getPluginPath() . DIRECTORY_SEPARATOR . $this->getSlug() . '.php';
    }

    /**
     * Returns absolute path to plugin root.
     *
     * @return string
     * @author Andreas Glaser
     */
    public function getPluginPath()
    {
        return WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getSlug();
    }

    /**
     * Lazy load controller methods.
     *
     * @param $className
     * @param $method
     *
     * @return \Closure
     * @author Andreas Glaser
     */
    public function controller($className, $method)
    {
        Expect::str($className);
        Expect::str($method);

        if (!class_implements($className, ControllerInterface::class)) {
            throw new \LogicException(sprintf('Controller "%s" does not implement "%s"', $className, ControllerInterface::class));
        }

        return function () use ($className, $method) {

            $arguments = func_get_args();

            if (isset($this->controllers[$className])) {

                if (!method_exists($this->controllers[$className], $method)) {
                    throw new \InvalidArgumentException(sprintf('Controller method %s does not exist', $method));
                }

                return call_user_func_array([$this->controllers[$className], $method], $arguments);
            }

            if (!class_exists($className)) {
                throw new \InvalidArgumentException(sprintf('Controller %s does not exist', $className));
            }

            $this->controllers[$className] = new $className($this);

            if (!method_exists($this->controllers[$className], $method)) {
                throw new \InvalidArgumentException(sprintf('Controller method %s does not exist', $method));
            }

            return call_user_func_array([$this->controllers[$className], $method], $arguments);
        };
    }

    /**
     * @param string $relativePath
     * @param array  $data
     *
     * @return \AndreasGlaser\Helpers\View\PHPView
     * @throws \Exception
     * @author Andreas Glaser
     */
    public function view($relativePath, array $data = [])
    {
        if (!$this->config['views_dir']) {
            throw new \Exception('"views_dir" not defined');
        }

        $viewPath = $this->config['views_dir'] . DIRECTORY_SEPARATOR . $relativePath;

        if (!is_file($viewPath)) {
            throw new \Exception(sprintf('View "%" not found', $viewPath));
        }

        if (!is_readable($viewPath)) {
            throw new \Exception(sprintf('View "%" is not readable', $viewPath));
        }

        return new PHPView($this->config['views_dir'] . DIRECTORY_SEPARATOR . $relativePath, $data);
    }

    /**
     * @param string $slug
     * @param null   $value
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addOption($slug, $value = null)
    {
        Expect::str($slug);
        add_option($this->makeSlug($slug), $value);

        return $this;
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function makeSlug(/* POLYMORPHIC */)
    {
        $pieces = ArrayHelper::merge([$this->slug], func_get_args());

        return implode($this->slugSeparator, $pieces);
    }

    /**
     * @param string $slug
     * @param null   $default
     *
     * @return mixed
     * @author Andreas Glaser
     */
    public function getOption($slug, $default = null)
    {
        Expect::str($slug);

        return get_option($this->makeSlug($slug), $default);
    }

    /**
     * @param $slug
     * @param $data
     *
     * @return mixed
     * @author Andreas Glaser
     */
    public function updateOption($slug, $data)
    {
        Expect::str($slug);

        return update_option($this->makeSlug($slug), $data);
    }

    /**
     * @param $slug
     *
     * @return mixed
     * @author Andreas Glaser
     */
    public function deleteOption($slug)
    {
        Expect::str($slug);

        return delete_option($this->makeSlug($slug));
    }

    public function enableTranslations()
    {
        $this->translate = true;

        return $this;
    }

    /**
     * Attaches controller to existing action/filter hook.
     *
     * @param string   $slug
     * @param callable $controller
     * @param int      $priority
     * @param int      $acceptedArgs
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addAction($slug, $controller, $priority = 10, $acceptedArgs = 1)
    {
        global $wp_filter;

        Expect::str($slug);
        Expect::isCallable($controller);

        if (!isset($wp_filter[$slug])) {
            throw new \LogicException(sprintf(
                'Action "%s" does not exist', $slug
            ));
        }

        add_action($slug, $controller, $priority, $acceptedArgs);

        return $this;
    }

    /**
     * Adds a new action/filter hook and prefixes its name
     *
     * @param string   $slug
     * @param callable $controller
     * @param int      $priority
     * @param int      $acceptedArgs
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function addActionCustom($slug, $controller, $priority = 10, $acceptedArgs = 1)
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        $slug = $this->makeSlug($slug);

        add_action($slug, $controller, $priority, $acceptedArgs);

        return $this;
    }

    public function addActionCustomWithDefault($slug, $defaultController, $arg = null, $priority = 10, $acceptedArgs = 1)
    {
        $this->addActionCustom($slug, $defaultController, $priority, $acceptedArgs);
        $this->triggerActionCustom($slug, $arg);

        return $this;
    }

    /**
     * Triggers existing action/filter hook.
     *
     * @param string $slug
     * @param mixed  $arg
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function triggerAction($slug, $arg = null)
    {
        global $wp_filter;

        Expect::str($slug);

        if (!isset($wp_filter[$slug])) {
            throw new \LogicException(sprintf(
                'Action "%s" does not exist', $slug
            ));
        }

        do_action($slug, $arg);

        return $this;
    }

    /**
     * Triggers custom action/filter hook.
     *
     * @param string $slug
     * @param mixed  $arg
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function triggerActionCustom($slug, $arg = null)
    {
        global $wp_filter;

        Expect::str($slug);
        $slug = $this->makeSlug($slug);

        if (!isset($wp_filter[$slug])) {
            throw new \LogicException(sprintf(
                'Action "%s" does not exist', $slug
            ));
        }

        do_action($slug, $arg);

        return $this;
    }

    /**
     * @param $slug
     * @param $controller
     * @param int $priority
     * @param int $accepted_args
     * @return $this
     *
     * @author Xavier Sanna
     */
    public function addFilterCustom($slug, $controller, $priority = 10, $accepted_args = 1)
    {
        Expect::str($slug);
        Expect::isCallable($controller);

        $slug = $this->makeSlug($slug);

        add_filter($slug, $controller, $priority, $accepted_args);

        return $this;
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function isActive()
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active($this->slug . '/' . $this->slug . '.php');
    }

    /**
     * Translate strings with variables e.g.
     *
     * print $plugin->lt('My name is %s', 'Hans');
     *
     *
     * @return string
     * @throws \Exception
     * @author Andreas Glaser
     */
    public function tl()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new \Exception('No aguments provided');
        }

        $string = $args[0];

        Expect::str($string);

        $data = ArrayHelper::removeFirstIndex($args);
        $string = $this->t($string);

        return vsprintf($string, $data);
    }

    /**
     * @param $string
     *
     * @return string|void
     * @author Andreas Glaser
     */
    public function t($string)
    {
        return __($string, $this->getSlug());
    }


    /**
     * Finds a model object by name and returns it
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getModel($name)
    {
        foreach($this->getModels() AS $model)
        {
            if(ucfirst($name) === array_pop(explode('\\', get_class($model)))) {
                return $model;
            }
        }

        throw new \Exception($name . ' model doesn\'t exists.');
    }

    /**
     * @return \MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu[]
     * @author Andreas Glaser
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @return array|\MoreSparetime\WordPress\PluginBuilder\Shortcode\Shortcode[]
     * @author Andreas Glaser
     */
    public function getShortcodes()
    {
        return $this->shortcodes;
    }

    /**
     * @return \MoreSparetime\WordPress\PluginBuilder\Ajax\Ajax[]
     * @author Andreas Glaser
     */
    public function getAjaxCalls()
    {
        return $this->ajaxCalls;
    }

    /**
     * @return \MoreSparetime\WordPress\PluginBuilder\Cron\Cron[]
     * @author Andreas Glaser
     */
    public function getCrons()
    {
        return $this->crons;
    }

    /**
     * @return \callable[]
     * @author Andreas Glaser
     */
    public function getActivationCallbacks()
    {
        return $this->activationCallbacks;
    }

    /**
     * @return \callable[]
     * @author Andreas Glaser
     */
    public function getDeactivationCallbacks()
    {
        return $this->deactivationCallbacks;
    }

    /**
     * @return \callable[]
     * @author Andreas Glaser
     */
    public function getUninstallCallbacks()
    {
        return $this->uninstallCallbacks;
    }

    /**
     * @return bool
     * @author Andreas Glaser
     */
    public function isTranslate()
    {
        return $this->translate;
    }

    /**
     * @return array
     * @author Andreas Glaser
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @return array
     * @author Andreas Glaser
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     * @author Andreas Glaser
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    /**
     * @return array
     * @author Andreas Glaser
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

}