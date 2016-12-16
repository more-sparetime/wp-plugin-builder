<?php

namespace MoreSparetime\WordPress\PluginBuilder;

/**
 * Class RenderableTrait
 *
 * @package MoreSparetime\WordPress\PluginBuilder
 * @author  Andreas Glaser
 */
trait RenderableTrait
{
    /**
     * @var RendererInterface|null
     */
    protected $renderer;

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\RendererInterface $renderer
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @return \MoreSparetime\WordPress\PluginBuilder\RendererInterface|null
     * @author Andreas Glaser
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function render()
    {
        if (!$this->renderer) {
            throw new \LogicException('Renderer not set');
        }

        return $this->renderer->render($this);
    }
}