<?php
/**
 * @author Rik van der Kemp <rik@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Menu;

use InvalidArgumentException;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class Builder
 *
 * @package Zicht\Bundle\MenuBundle\Menu
 */
interface BuilderInterface
{
    /**
     * Set the menu names to preload
     *
     * @param array $menus
     */
    public function setPreloadMenus($menus);

    /**
     * Create the menu based on the doctrine model.
     *
     * @param string $name
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return ItemInterface
     *
     * @throws \InvalidArgumentException
     */
    public function build($name, Request $request);

    /**
     * Get the root item based on the specified name and request.
     *
     * @param string $name
     * @param Request $request
     * @return mixed
     */
    public function getRootItemByName($name, $request);

    /**
     * Check if a root item exists.
     *
     * @param string $name
     * @param Request $request
     * @return bool
     */
    public function hasRootItemByName($name, $request);

    /**
     * Create menu
     *
     * @param Request $request
     * @param ItemInterface $root
     * @return ItemInterface
     * @throws InvalidArgumentException
     */
    public function createMenu($request, $root);

    /**
     * Add menu item hierarchy
     *
     * @param Request $request
     * @param mixed $children
     * @param ItemInterface $parent
     * @return int
     */
    public function addMenuItemHierarchy($request, $children, $parent);

    /**
     * Utility method to convert MenuItem's from the doctrine model to Knp MenuItems
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $item
     * @param MenuItem $menu
     *
     * @return ItemInterface
     */
    public function addMenuItem(Request $request, array $item, MenuItem $menu);

    /**
     * Adds an item on the fly that was not originally in the menu.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param ItemInterface $item
     * @return void
     */
    public function addGhostItem(Request $request, $item);
}