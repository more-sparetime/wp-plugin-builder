<?php

namespace MoreSparetime\WordPress\PluginBuilder;

/**
 * Interface AttachableInterface
 *
 * @package MoreSparetime\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
interface AttachableInterface
{

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks();
}