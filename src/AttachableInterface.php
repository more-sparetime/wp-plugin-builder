<?php

namespace AndreasGlaser\WordPress\PluginBuilder;

/**
 * Interface AttachableInterface
 *
 * @package AndreasGlaser\WordPress\PluginBuilder
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