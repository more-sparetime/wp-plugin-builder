<?php

namespace AndreasGlaser\WordPress\PluginBuilder\Admin;

use AndreasGlaser\Helpers\HtmlHelper;
use AndreasGlaser\WordPress\PluginBuilder\PluginAwareTrait;
use AndreasGlaser\WordPress\PluginBuilder\PluginInterface;
use AndreasGlaser\WordPress\PluginBuilder\RenderableTrait;
use AndreasGlaser\WordPress\PluginBuilder\Tools\Expect;

/**
 * Class Page
 *
 * @package AndreasGlaser\WordPress\PluginBuilder\Admin
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
    protected $callback;

    /**
     * @var string
     */
    protected $capability;

    public function __construct(PluginInterface $plugin, $slug, $title, $callback = null, $capability = 'manage_options')
    {
        $this->setPlugin($plugin);
        $this->setSlug($slug);
        $this->title = $title;

        if (!$callback) {
            $callback = [$this, 'render'];
        }

        $this->callback = $callback;
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
    public function getCallback()
    {
        return $this->callback;
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