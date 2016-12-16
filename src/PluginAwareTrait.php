<?php

namespace MoreSparetime\WordPress\PluginBuilder;

/**
 * Interface AttachableInterface
 *
 * @package MoreSparetime\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
trait PluginAwareTrait
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\PluginInterface $plugin
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
     * @return \MoreSparetime\WordPress\PluginBuilder\Plugin
     * @author Andreas Glaser
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
}