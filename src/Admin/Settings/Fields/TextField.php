<?php

namespace AndreasGlaser\WordPress\PluginBuilder\Admin\Settings\Fields;

use AndreasGlaser\Helpers\Html\FormHelper;
use AndreasGlaser\WordPress\PluginBuilder\Admin\Settings\Field;

/**
 * Class TextField
 *
 * @package AndreasGlaser\WordPress\PluginBuilder\Admin\Settings\Fields
 * @author  Andreas Glaser
 */
class TextField extends Field
{
    /**
     * @author Andreas Glaser
     */
    public function render()
    {
        if (!$this->renderer) {
            print FormHelper::text($this->getOptionName(), get_option($this->getOptionName()), ['id' => $this->getOptionName()]);
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