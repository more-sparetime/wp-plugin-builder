<?php

namespace MoreSparetime\WordPress\PluginBuilder;

use AndreasGlaser\Helpers\ArrayHelper;
use AndreasGlaser\Helpers\Validate\Expect;
use AndreasGlaser\HelpersArrayHelper;
use MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu;
use MoreSparetime\WordPress\PluginBuilder\Ajax\Ajax;
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
    protected $prefix = 'my_plugin';

    /**
     * @var string
     */
    protected $prefixSeparator = '_';

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
     * Plugin constructor.
     *
     * @param $prefix
     *
     * @author Andreas Glaser
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getPrefixSeparator()
    {
        return $this->prefixSeparator;
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function makeSlug(/* POLYMORPHIC */)
    {
        $pieces = ArrayHelper::merge([$this->prefix], func_get_args());

        return implode($this->prefixSeparator, $pieces);
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
     * @param \MoreSparetime\WordPress\PluginBuilder\Shortcode\Shortcode $shortcode
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addShortcode(Shortcode\Shortcode $shortcode)
    {
        if (!in_array($shortcode, $this->shortcodes)) {
            $this->shortcodes [] = $shortcode;
        }

        return $this;
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
     * Shorthand
     *
     * @param string   $slug
     * @param callable $callback
     * @param string   $recurrence
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function cron($slug, $callback, $recurrence = 'hourly')
    {
        Expect::str($slug);
        Expect::isCallable($callback);
        Expect::str($recurrence);

        return $this->addCron(new Cron($this, $slug, $callback, $recurrence));
    }

    /**
     * Helper
     *
     * @param string   $slug
     * @param callable $callback
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function shortcode($slug, $callback)
    {
        Expect::str($slug);
        Expect::isCallable($callback);

        return $this->addShortcode(new Shortcode\Shortcode($this, $slug, $callback));
    }

    /**
     * @param \MoreSparetime\WordPress\PluginAjax $ajax
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
     * Helper for adding ajax calls.
     *
     * @param string   $slug
     * @param callable $callback
     * @param bool     $internal
     * @param bool     $external
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCall($slug, $callback, $internal = true, $external = false)
    {
        Expect::str($slug);
        Expect::isCallable($callback);
        Expect::bool($internal);
        Expect::bool($external);

        return $this->addAjaxCall(new Ajax($this, $slug, $callback, $internal, $external));
    }

    /**
     * Adds ajax call, available for admin pages.
     *
     * @param $slug
     * @param $callback
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallInternal($slug, $callback)
    {
        Expect::str($slug);
        Expect::isCallable($callback);

        return $this->addAjaxCall(new Ajax($this, $slug, $callback, true, false));
    }

    /**
     * Adds ajax call available for external user front-end.
     *
     * @param $slug
     * @param $callback
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallExternal($slug, $callback)
    {
        Expect::str($slug);
        Expect::isCallable($callback);

        return $this->addAjaxCall(new Ajax($this, $slug, $callback, false, true));
    }

    /**
     * Adds ajax call available for both admin pages and external user front-end.
     *
     * @param $slug
     * @param $callback
     *
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function ajaxCallGlobal($slug, $callback)
    {
        Expect::str($slug);
        Expect::isCallable($callback);

        return $this->addAjaxCall(new Ajax($this, $slug, $callback, true, true));
    }

    /**
     * @param $callback
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addActivationCallback($callback)
    {
        Expect::isCallable($callback);

        if (!in_array($callback, $this->activationCallbacks)) {
            $this->activationCallbacks[] = $callback;
        }

        return $this;
    }

    /**
     * @param $callback
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addDeactivationCallback($callback)
    {
        Expect::isCallable($callback);

        if (!in_array($callback, $this->deactivationCallbacks)) {
            $this->deactivationCallbacks[] = $callback;
        }

        return $this;
    }

    /**
     * @param $callback
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addUninstallCallback($callback)
    {
        Expect::isCallable($callback);

        if (!in_array($callback, $this->uninstallCallbacks)) {
            $this->uninstallCallbacks[] = $callback;
        }

        return $this;
    }

    /**
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        if ($this->translate) {
            add_action('plugins_loaded', function () {
                load_plugin_textdomain(
                    $this->getPrefix(),
                    false,
                    $this->getPrefix() . '/assets/languages/'
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
     * @return mixed
     * @author Andreas Glaser
     */
    public function isActive()
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active($this->prefix . '/' . $this->prefix . '.php');
    }

    /**
     * Returns absolute path to plugin root.
     *
     * @return string
     * @author Andreas Glaser
     */
    public function getPluginPath()
    {
        return WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getPrefix();
    }

    /**
     * Returns absolute path to plugin root file.
     *
     * @return string
     * @author Andreas Glaser
     */
    public function getPluginFilePath()
    {
        return $this->getPluginPath() . DIRECTORY_SEPARATOR . $this->getPrefix() . '.php';
    }
}