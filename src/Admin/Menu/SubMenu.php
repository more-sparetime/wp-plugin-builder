<?php

namespace MoreSparetime\WordPress\PluginBuilder\Admin\Menu;

use MoreSparetime\WordPress\PluginBuilder\Admin\Page;
use MoreSparetime\WordPress\PluginBuilder\AttachableInterface;

/**
 * Class SubMenu
 *
 * @package MoreSparetime\WordPress\PluginBuilder\Admin\SubMenu
 * @author  Andreas Glaser
 */
class SubMenu implements AttachableInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var \MoreSparetime\WordPress\PluginBuilder\Admin\Menu\Menu
     */
    protected $menu;

    /**
     * @var \MoreSparetime\WordPress\PluginBuilder\Admin\Page
     */
    protected $page;

    public function __construct(Menu $menu, Page $page, $title)
    {
        $this->menu = $menu;
        $this->page = $page;
        $this->title = $title;
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
            add_submenu_page(
                $this->menu->getPage()->getSlug(),
                $this->page->getTitle(),
                $this->title,
                $this->page->getCapability(),
                $this->page->getSlug(),
                $this->page->getController()
            );
        });
    }
}

