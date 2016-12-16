<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin\Settings;

use MoreSparetime\WordPress\PluginBuilder\Admin\Page;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;
use MoreSparetime\WordPress\PluginBuilder\Plugin;
use MoreSparetime\WordPress\PluginBuilder\RenderableTrait;

/**
 * Class Field
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin\Settings
 * @author  Andreas Glaser
 */
abstract class Field implements AttachableInterface
{
    use RenderableTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * Field constructor.
     *
     * @param \MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Section $section
     * @param string                                                        $slug
     * @param string                                                        $title
     *
     * @author Andreas Glaser
     */
    public function __construct(Section $section, $slug, $title)
    {
        $this->setSection($section);
        $this->setSlug($slug);
        $this->title = $title;
    }

    /**
     * @param \MoreSparetime\WordPress\PluginBuilder\Admin\Settings\Section $section
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setSection(Section $section)
    {
        $this->section = $section;
        $this->page = $this->section->getPage();
        $this->plugin = $this->page->getPlugin();

        return $this;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $slug
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setSlug($slug)
    {
        $this->slug = $this->section->getSlug() . $this->plugin->getSlugSeparator() . $slug;

        return $this;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Field
     * @author Andreas Glaser
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    protected function getOptionName()
    {
        return $this->getSlug();
    }

    /**
     * @return mixed
     * @author Andreas Glaser
     */
    public function getOptionValue()
    {
        return get_option($this->getOptionName(), null);
    }

    /**
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        add_action('admin_init', function () {

            register_setting(
                $this->section->getGroupName(),
                $this->getSlug(),
                [$this, 'sanitize']
            );

            add_settings_field(
                $this->slug,
                $this->title,
                [$this, 'render'],
                $this->section->getPage()->getSlug(),
                $this->section->getSlug()
            );
        });
    }

    /**
     * @param $input
     *
     * @return mixed
     * @author Andreas Glaser
     */
    abstract public function sanitize($input);
}