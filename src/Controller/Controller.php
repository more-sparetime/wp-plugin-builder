<?php

namespace MoreSparetime\WordPress\PluginBuilder\Controller;

use MoreSparetime\WordPress\PluginBuilder\PluginInterface;

/**
 * Class Controller
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Controller
 * @author  Andreas Glaser
 */
abstract class Controller implements ControllerInterface
{
    use ControllerTrait;

    /**
     * Controller constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\PluginInterface $plugin
     *
     * @author Andreas Glaser
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->setPlugin($plugin);
    }
}