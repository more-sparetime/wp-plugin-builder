<?php

namespace MoreSparetime\WordPress\PluginBuilder;

/**
 * Interface RendererInterface
 *
 * @package MoreSparetime\WordPress\PluginBuilder
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