<?php

namespace MoreSparetime\WordPress\PluginBuilder\Controller;

/**
 * Interface ControllerInterface
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Controller
 * @author  Andreas Glaser
 */
interface ControllerInterface
{
    /**
     * @param string $message
     * @param string $group
     * @param null   $data
     *
     * @return mixed
     * @author Andreas Glaser
     */
    public function addError($message, $group = 'request-validation', $data = null);

    /**
     * @return \WP_Error
     * @author Andreas Glaser
     */
    public function getErrors();
}