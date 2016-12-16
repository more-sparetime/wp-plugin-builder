<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Fields;

use MoreSparetime\Helpers\Html\FormHelper;
use MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Field;

/**
 * Class TextareaField
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Fields
 * @author  Andreas Glaser
 */
class TextareaField extends Field
{
    public function render()
    {
        if (!$this->renderer) {
            print FormHelper::textarea($this->getOptionName(), get_option($this->getOptionName()), ['id' => $this->getOptionName()]);
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