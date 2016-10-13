<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Menu;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Menu\FactoryInterface;
use InvalidArgumentException;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class Builder
 *
 * @package Zicht\Bundle\MenuBundle\Menu
 */
class Builder implements ContainerAwareInterface
{
    /* @codingStandardsIgnoreStart */
    use ContainerAwareTrait;
    /* @codingStandardsIgnoreEnd */

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Gedmo\Tree\Entity\Repository\NestedTreeRepository
     */
    protected $menuItemEntity;

    /**
     * @var Registry
     */
    protected $em;

    /**
     * Builder constructor.
     *
     * @param FactoryInterface $factory
     * @param Registry $doctrine
     * @param string $entity
     */
    public function __construct(FactoryInterface $factory, Registry $doctrine, $entity = 'ZichtMenuBundle:MenuItem')
    {
        $this->factory = $factory;
        $this->em = $doctrine->getManager();
        $this->menuItemEntity = $this->em->getRepository($entity);
    }

    /**
     * Create the menu based on the doctrine model.
     *
     * @param string $name
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return ItemInterface
     *
     * @throws \InvalidArgumentException
     */
    public function build($name, Request $request)
    {
        $root = $this->getRootItemByName($name, $request);

        if (null === $root) {
            throw new \InvalidArgumentException("Could not find root item with name '$name'");
        }

        return $this->createMenu($request, $root);
    }

    /**
     * Get the root item based on the specified name and request.
     *
     * @param string $name
     * @param Request $request
     * @return mixed
     */
    public function getRootItemByName($name, $request)
    {
        $ret = null;
        $params = array(
            'parent' => null,
            'name'   => $name
        );
        if ($request->get('_locale')) {
            $params['language']= $request->get('_locale');
            $ret = $this->menuItemEntity->findOneBy($params);

            // Fallback to "no locale".
            if (!$ret) {
                unset($params['language']);
            }
        }
        if (!$ret) {
            $ret = $this->menuItemEntity->findOneBy($params);
        }
        return $ret;
    }

    /**
     * Create menu
     *
     * @param Request $request
     * @param ItemInterface $root
     * @return ItemInterface
     * @throws InvalidArgumentException
     */
    public function createMenu($request, $root)
    {
        if (!$root) {
            throw new InvalidArgumentException("Invalid root item");
        }
        $menu = $this->factory->createItem('root');
        $this->addMenuItemHierarchy($request, $this->menuItemEntity->childrenHierarchy($root), $menu);

        // 1.x compatibility
        if (is_callable($menu, 'setCurrentUri')) {
            $menu->setCurrentUri($request->getRequestUri());
        }

        return $menu;
    }

    /**
     * Add menu item hierarchy
     *
     * @param Request $request
     * @param mixed $children
     * @param ItemInterface $parent
     * @return int
     */
    public function addMenuItemHierarchy($request, $children, $parent)
    {
        $ret = 0;
        foreach ($children as $child) {
            $ret ++;
            $item = $this->addMenuItem($request, $child, $parent);
            if (!empty($child['__children'])) {
                $ret += $this->addMenuItemHierarchy($request, $child['__children'], $item);
            }
        }
        return $ret;
    }

    /**
     * Utility method to convert MenuItem's from the doctrine model to Knp MenuItems
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $item
     * @param MenuItem $menu
     *
     * @return ItemInterface
     */
    public function addMenuItem(Request $request, array $item, MenuItem $menu)
    {
        $attributes = array();

        // if the menu item has a name, add it as a css class.
        if ($name = $item['name']) {
            $attributes['class'] = $name;
        }

        if (preg_match('!^(?:https?://|mailto:)!', $item['path'])) {
            $uri = $item['path'];
        } else {
            $baseUrl = $request->getBaseUrl();
            $uri = $baseUrl . '/' . ltrim($item['path'], '/');
        }

        $menuItem = $menu->addChild(
            $item['id'],
            array(
                'uri'        => $uri,
                'attributes' => $attributes,
                'label'      => $item['title']
            )
        );

        $menuItem->setExtras($item);
        return $menuItem;
    }

    /**
     * Adds an item on the fly that was not originally in the menu.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param ItemInterface $item
     * @return void
     */
    public function addGhostItem(Request $request, $item)
    {
        $item->addChild(
            $this->factory->createItem(
                $item['id'],
                array(
                    'uri' => $request->getRequestUri(),
                    'display' => false,
                    'label' => $item['title']
                )
            )
        );
    }
}
