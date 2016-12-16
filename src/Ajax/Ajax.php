<?php

namespace MoreSparetime\WordPress\PluginBuilder\Ajax;

use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;
use MoreSparetime\WordPress\PluginBuilder\PluginInterface;
use MoreSparetime\WordPress\PluginBuilder\Tools\Expect;

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
    protected $callback;

    /**
     * Ajax constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\PluginInterface $plugin
     * @param                                                        $slug
     * @param                                                        $callback
     * @param bool                                                   $internal
     * @param bool                                                   $external
     *
     * @author Andreas Glaser
     */
    public function __construct(PluginInterface $plugin, $slug, $callback, $internal = true, $external = false)
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setCallback($callback);
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
     * @param callable $callback
     *
     * @return $this
     * @author Andreas Glaser
     */
    protected function setCallback($callback)
    {
        Expect::isCallable($callback);

        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     * @author Andreas Glaser
     */
    protected function getCallback()
    {
        return $this->callback;
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
            add_action('wp_ajax_' . $this->getSlug(), $this->callback);
        }

        if ($this->getExternal()) {
            add_action('wp_ajax_nopriv_' . $this->getSlug(), $this->callback);
        }
    }
}