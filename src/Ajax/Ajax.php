<?php

namespace MoreSparetime\WordPress\PluginBuilder\Ajax;

use AndreasGlaser\Helpers\Validate\Expect;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;
use MoreSparetime\WordPress\PluginBuilder\PluginInterface;

/**
 * Class Call
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Shortcode
 * @author  Andreas Glaser
 */
class Ajax implements AttachableInterface
{
    use PluginAwareTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var boolean
     */
    protected $internal;

    /**
     * @var boolean
     */
    protected $external;

    /**
     * @var callable
     */
    protected $controller;

    /**
     * Ajax constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\PluginInterface $plugin
     * @param string                                                 $slug
     * @param callable                                               $controller
     * @param bool                                                   $internal
     * @param                                                        bool
     *
     * @author Andreas Glaser
     */
    public function __construct(PluginInterface $plugin, $slug, $controller, $internal = true, $external = false)
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setController($controller);
        $this->setInternal((bool)$internal);
        $this->setExternal((bool)$external);
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $slug
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setSlug($slug)
    {
        Expect::str($slug);

        $this->slug = $this->plugin->makeSlug($slug);

        return $this;
    }

    /**
     * @param callable $controller
     *
     * @return $this
     * @author Andreas Glaser
     */
    protected function setController($controller)
    {
        Expect::isCallable($controller);

        $this->controller = $controller;

        return $this;
    }

    /**
     * @return callable
     * @author Andreas Glaser
     */
    protected function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * @param bool $internal
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setInternal($internal)
    {
        Expect::bool($internal);

        $this->internal = $internal;

        return $this;
    }

    /**
     * @return boolean
     * @author Andreas Glaser
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @param boolean $external
     *
     * @return Ajax
     * @author Andreas Glaser
     */
    public function setExternal($external)
    {
        Expect::bool($external);

        $this->external = (boolean)$external;

        return $this;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        if ($this->getInternal()) {
            add_action('wp_ajax_' . $this->getSlug(), $this->controller);
        }

        if ($this->getExternal()) {
            add_action('wp_ajax_nopriv_' . $this->getSlug(), $this->controller);
        }
    }
}