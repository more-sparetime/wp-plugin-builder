<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin;

use MoreSparetime\Helpers\Html\FormHelper;
use MoreSparetime\Helpers\HtmlHelper;
use MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Section;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;

/**
 * Class SettingsPage
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin
 * @author  Andreas Glaser
 */
class SettingsPage extends Page implements AttachableInterface
{
    /**
     * @var Section[]
     */
    protected $sections = [];

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Section $section
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addSection(Section $section)
    {
        if (!in_array($section, $this->sections)) {
            $section->setPage($this);
            $this->sections[] = $section;
        }

        return $this;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        foreach ($this->sections AS $section) {
            if ($section instanceof AttachableInterface) {
                $section->attachHooks();
            }
        }
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function render()
    {
        if (!$this->renderer) {

            $form = '';
            foreach ($this->sections AS $section) {
                ob_start();
                settings_fields($section->getGroupName());
                $form .= ob_get_clean();
            }

            ob_start();
            do_settings_sections($this->getSlug());
            submit_button();
            $form .= ob_get_clean();

            echo HtmlHelper::div(
                HtmlHelper::h2($this->getTitle()) .
                FormHelper::open('options.php', 'POST') .
                $form .
                FormHelper::close(),
                ['class' => 'wrap']
            );

        } else {
            echo $this->renderer->render($this);
        }
    }
}