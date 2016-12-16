<?php

namespace MoreSparetime\WordPress\PluginBuilder\Shortcode;

use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\Plugin;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;
use MoreSparetime\WordPress\PluginBuilder\Tools\Expect;

/**
 * Class Shortcode
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Shortcode
 * @author  Andreas Glaser
 */
class Shortcode implements AttachableInterface
{
    use PluginAwareTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var callable
     */
    protected $callback;

    public function __construct(Plugin $plugin, $slug, $callback, array $defaults = [])
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setCallback($callback);

        $this->defaults = $defaults;
    }

    /**
     * Alias
     *
     * @return string
     * @author Andreas Glaser
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
     * @return array
     * @author Andreas Glaser
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param $callback
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setCallback($callback)
    {
        Expect::isCallable($callback);
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     * @author Andreas Glaser
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        if (defined('DOING_AJAX')) {
            return;
        }

        add_shortcode($this->getName(), [$this, 'dispatch']);
    }

    /**
     * @param $args
     *
     * @param $content
     * @param $tag
     *
     * @author Andreas Glaser
     */
    public function dispatch($args, $content, $tag)
    {
        $attributes = shortcode_atts($this->getDefaults(), $args, $this->getName());
        call_user_func_array($this->callback, ['shortcode' => $this, 'attributes' => $attributes, 'content' => $content]);
    }
}