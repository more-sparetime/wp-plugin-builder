<?php

namespace AndreasGlaser\WordPress\PluginBuilder\Admin\Settings;

use AndreasGlaser\WordPress\PluginBuilder\Admin\Page;
use AndreasGlaser\WordPress\PluginBuilder\Admin\SettingsPage;
use AndreasGlaser\WordPress\PluginBuilder\AttachableInterface;

class Section implements AttachableInterface
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \AndreasGlaser\WordPress\PluginBuilder\Admin\SettingsPage
     */
    protected $page;

    /**
     * Section constructor.
     *
     * @param \AndreasGlaser\WordPress\PluginBuilder\Admin\SettingsPage $page
     * @param string                                                    $slug
     * @param string                                                    $title
     *
     * @author Andreas Glaser
     */
    public function __construct(SettingsPage $page, $slug, $title)
    {
        $this->page = $page;
        $this->setSlug($slug);
        $this->title = $title;

    }

    /**
     * @param $slug
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setSlug($slug)
    {
        $this->slug = $this->page->getSlug() . $this->page->getPlugin()->getPrefixSeparator() . $slug;

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
     * @return string
     * @author Andreas Glaser
     */
    public function getGroupName()
    {
        return $this->getSlug() . $this->page->getPlugin()->getPrefixSeparator() . 'group';
    }

    /**
     * @param \AndreasGlaser\WordPress\PluginBuilder\Admin\Settings\Field $field
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addField(Field $field)
    {
        if (!in_array($field, $this->fields)) {
            $field->setSection($this);
            $this->fields[] = $field;
        }

        return $this;
    }

    /**
     * @author Andreas Glaser
     */
    public function renderSection()
    {
        print $this->title;
    }

    /**
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        add_action('admin_init', function () {

            add_settings_section(
                $this->getSlug(),
                $this->title,
                [$this, 'renderSection'],
                $this->page->getSlug()
            );
        });

        foreach ($this->fields AS $field) {
            if ($field instanceof AttachableInterface) {
                $field->attachHooks();
            }
        }
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
     * @param \AndreasGlaser\WordPress\PluginBuilder\Admin\Page $page
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return \AndreasGlaser\WordPress\PluginBuilder\Admin\Page
     * @author Andreas Glaser
     */
    public function getPage()
    {
        return $this->page;
    }
}