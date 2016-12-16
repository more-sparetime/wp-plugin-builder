<?php

namespace MoreSparetime\WordPress\PluginBuilder\Shortcode;

use AndreasGlaser\Helpers\Validate\Expect;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\Plugin;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;

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
    protected $context;

    /**
     * @var callable
     */
    protected $controller;

    /**
     * Shortcode constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\Plugin $plugin
     * @param string                                        $slug
     * @param callable                                      $controller
     * @param array                                         $context
     *
     * @author Andreas Glaser
     */
    public function __construct(Plugin $plugin, $slug, $controller, array $context = [])
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->setController($controller);

        $this->context = $context;
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
    public function getContext()
    {
        return $this->context;
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
        if (defined('DOING_AJAX')) {
            return;
        }

        add_shortcode($this->getName(), [$this, 'dispatch']);
    }

    /**
     * @param $args
     * @param $content
     * @param $tag
     *
     * @author Andreas Glaser
     */
    public function dispatch($args, $content, $tag)
    {
        $attributes = shortcode_atts($this->getContext(), $args, $this->getName());
        call_user_func_array($this->controller, ['shortcode' => $this, 'attributes' => $attributes, 'content' => $content, 'tag' => $tag]);
    }
}