<?php

namespace AndreasGlaser\WordPress\PluginBuilder\Admin\Menu;

use AndreasGlaser\WordPress\PluginBuilder\Admin\Page;
use AndreasGlaser\WordPress\PluginBuilder\AttachableInterface;

/**
 * Class Menu
 *
 * @package AndreasGlaser\WordPress\PluginBuilder\Admin\Menu
 * @author  Andreas Glaser
 */
class Menu implements AttachableInterface
{
    /**
     * @var float
     */
    protected $position;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \AndreasGlaser\WordPress\PluginBuilder\Admin\Page
     */
    protected $page;

    /**
     * @var SubMenu[]
     */
    protected $subMenus = [];

    /**
     * Menu constructor.
     *
     * @param \AndreasGlaser\WordPress\PluginBuilder\Admin\Page $page
     * @param string                                            $title
     * @param string                                            $icon
     * @param float                                             $position
     *
     * @author Andreas Glaser
     */
    public function __construct(Page $page, $title, $icon = null, $position = null)
    {
        $this->page = $page;
        $this->title = $title;
        $this->icon = $icon;
        $this->position = $position;
    }

    /**
     * @param \AndreasGlaser\WordPress\PluginBuilder\Admin\Menu\SubMenu $subMenu
     *
     * @return $this
     * @author Andreas Glaser
     */
    public function addSubMenu(SubMenu $subMenu)
    {
        if (!in_array($subMenu, $this->subMenus)) {
            $this->subMenus[] = $subMenu;
        }

        return $this;
    }

    /**
     * @return \AndreasGlaser\WordPress\PluginBuilder\Admin\Menu\SubMenu[]
     * @author Andreas Glaser
     */
    public function getSubMenus()
    {
        return $this->subMenus;
    }

    /**
     * @return float
     * @author Andreas Glaser
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     * @author Andreas Glaser
     */
    public function getIcon()
    {
        return $this->icon;
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
     * @return \AndreasGlaser\WordPress\PluginBuilder\Admin\Page
     * @author Andreas Glaser
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return void
     * @author Andreas Glaser
     */
    public function attachHooks()
    {
        if (defined('DOING_AJAX')) {
            return;
        }

        if ($this->page instanceof AttachableInterface) {
            $this->page->attachHooks();
        }

        add_action('admin_menu', function () {
            add_menu_page(
                $this->page->getTitle(),
                $this->title,
                $this->page->getCapability(),
                $this->page->getSlug(),
                $this->page->getCallback(),
                $this->icon,
                $this->position
            );
        });

        foreach ($this->subMenus AS $subMenu) {
            $subMenu->attachHooks();
        }
    }
}

