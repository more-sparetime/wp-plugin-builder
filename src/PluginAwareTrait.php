<?php

namespace AndreasGlaser\WordPress\PluginBuilder;

/**
 * Interface AttachableInterface
 *
 * @package AndreasGlaser\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
trait PluginAwareTrait
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @param \AndreasGlaser\WordPress\PluginBuilder\PluginInterface $plugin
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setPlugin(PluginInterface $plugin)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * @return \AndreasGlaser\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
}