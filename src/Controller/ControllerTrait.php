<?php

namespace MoreSparetime\WordPress\PluginBuilder\Controller;

use MoreSparetime\WordPress\PluginBuilder\PluginAwareTrait;

/**
 * Class ControllerTrait
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Controller
 * @author  Andreas Glaser
 */
trait ControllerTrait
{
    use PluginAwareTrait;

    /**
     * @var \WP_Error|null
     */
    protected $errors;

    /**
     * @param        $message
     * @param string $group
     * @param null   $data
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addError($message, $group = 'request-validation', $data = null)
    {
        if (!$this->errors) {
            $this->errors = new \WP_Error($group, $message, $data);
        } else {
            $this->errors->add($group, $message, $data);
        }

        return $this;
    }

    /**
     * @return null|\WP_Error
     * @author Andreas Glaser
     */
    public function getErrors()
    {
        return $this->errors;
    }
}