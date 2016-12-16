<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Fields;

use MoreSparetime\Helpers\Html\FormHelper;
use MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Field;

/**
 * Class SelectField
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Fields
 * @author  Andreas Glaser
 */
class SelectField extends Field
{
    /**
     * @var
     */
    protected $options;

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function render()
    {
        if (!$this->renderer) {
            print FormHelper::select($this->getOptionName(), $this->options, get_option($this->getOptionName()), ['id' => $this->getOptionName()]);
        } else {
            print $this->renderer->render($this);
        }
    }

    /**
     * @param $input
     *
     * @return string
     * @author Andreas Glaser
     */
    public function sanitize($input)
    {
        return sanitize_text_field($input);
    }
}