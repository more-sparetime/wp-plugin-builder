<?php

namespace AndreasGlaser\WordPress\PluginBuilder;

/**
 * Interface RendererInterface
 *
 * @package AndreasGlaser\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
interface RendererInterface
{
    /**
     * @param $data
     *
     * @return string
     * @author Andreas Glaser
     */
    public function render($data);
}