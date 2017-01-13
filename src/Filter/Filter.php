<?php

namespace MoreSparetime\WordPress\PluginBuilder\Filter;

use AndreasGlaser\Helpers\Validate\Expect;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\Plugin;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;

/**
 * Class Filter
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Shortcode
 * @author  Andreas Glaser
 */
class Filter implements AttachableInterface
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
     * Filter constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\Plugin $plugin
     * @param string                                        $slug
     * @param callable                                      $controller
     *
     * @author Xavier Sanna
     */
    public function __construct(Plugin $plugin, $slug, $controller)
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setController($controller);
    }

    /**
     * Alias
     *
     * @return string
     * @author Xavier Sanna
     */
    public function getName()
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
     * @return string
     * @author Andreas Glaser
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $controller
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setController($controller)
    {
        Expect::isCallable($controller);
        $this->controller = $controller;

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
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        //todo
    }
}