<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin;

use AndreasGlaser\Helpers\HtmlHelper;
use AndreasGlaser\Helpers\Validate\Expect;
use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;
use MoreSparetime\WordPress\PluginBuilder\PluginInterface;
use MoreSparetime\WordPress\PluginBuilder\RenderableTrait;

/**
 * Class Page
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin
 * @author  Andreas Glaser
 */
class Page
{
    use PluginAwareTrait;
    use RenderableTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var callable|null
     */
    protected $controller;

    /**
     * @var string
     */
    protected $capability;

    /**
     * Page constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\PluginInterface $plugin
     * @param string                                                 $slug
     * @param string                                                 $title
     * @param callable|null                                          $controller
     * @param string                                                 $capability
     *
     * @author Andreas Glaser
     */
    public function __construct(PluginInterface $plugin, $slug, $title, $controller = null, $capability = 'manage_options')
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->title = $title;

        if (!$controller) {
            $controller = [$this, 'render'];
        }

        $this->controller = $controller;
        $this->capability = $capability;
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
    public function getTitle()
    {
        return $this->title;
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
     * @return string
     * @author Andreas Glaser
     */
    public function getCapability()
    {
        return $this->capability;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function render()
    {
        if (!$this->renderer) {
            echo HtmlHelper::h2($this->title);
        } else {
            echo $this->renderer->render($this);
        }
    }
}