<?php

namespace MoreSparetime\WordPress\PluginBuilder\Cron;

use AndreasGlaser\Helpers\Validate\Expect;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\Plugin;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;

/**
 * Class Cron
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Cron
 * @author  Andreas Glaser
 */
class Cron implements AttachableInterface
{
    use PluginAwareTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var callable
     */
    protected $controller;

    /**
     * @var string
     */
    protected $recurrence;

    /**
     * Cron constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\Plugin $plugin
     * @param string                                        $slug
     * @param callable                                      $controller
     * @param string                                        $recurrence
     *
     * @author Andreas Glaser
     */
    public function __construct(Plugin $plugin, $slug, $controller, $recurrence = 'hourly')
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setController($controller);
        $this->setRecurrence($recurrence);
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
     * @param string $slug
     *
     * @return Cron
     * @author Andreas Glaser
     */
    public function setSlug($slug)
    {
        Expect::str($slug);
        $this->slug = $this->plugin->makeSlug('cron', $slug);

        return $this;
    }

    /**
     * @return callable
     * @author Andreas Glaser
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param callable $controller
     *
     * @return Cron
     * @author Andreas Glaser
     */
    public function setController($controller)
    {
        Expect::isCallable($controller);

        $this->controller = $controller;

        return $this;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * @param string $recurrence
     *
     * @return Cron
     * @author Andreas Glaser
     */
    public function setRecurrence($recurrence)
    {
        Expect::str($recurrence);
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        // todo: this can probably be wrapped into activation hook
        if (!wp_next_scheduled($this->slug)) {
            wp_schedule_event(time(), $this->recurrence, $this->slug);
        }

        add_action($this->slug, $this->controller);

        // remove cron on deactivation
        register_deactivation_hook($this->plugin->getPluginFilePath(), function () {
            wp_clear_scheduled_hook($this->slug);
        });
    }
}