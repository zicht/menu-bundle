<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Menu;

use \Knp\Menu\FactoryInterface;
use \InvalidArgumentException;
use \Symfony\Component\DependencyInjection\ContainerAware;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 *
 */
class Builder extends ContainerAware
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Gedmo\Tree\Entity\Repository\NestedTreeRepository
     */
    protected $menuItemEntity;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $em;


    /**
     * @param FactoryInterface $factory
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param string $entity
     */
    public function __construct(FactoryInterface $factory, $doctrine, $entity = 'ZichtMenuBundle:MenuItem')
    {
        $this->factory = $factory;
        $this->em = $doctrine->getManager();
        $this->menuItemEntity = $this->em->getRepository($entity);;
    }


    /**
     * Create the menu based on the doctrine model.
     *
     * @param string $name
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Knp\Menu\ItemInterface
     */
    function build($name, Request $request)
    {
        $root = $this->menuItemEntity->findOneBy(array(
            'parent' => null,
            'name' => $name
        ));

        if (null === $root) {
            throw new \InvalidArgumentException("Could not find root item with name '$name'");
        }

        return $this->createMenu($request, $root);
    }


    /**
     * @param $request
     * @param $root
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function createMenu($request, $root)
    {
        if (!$root) {
            var_dump($root);
            throw new InvalidArgumentException("Invalid root item");
        }
        $menu = $this->factory->createItem('root');
        $this->addMenuItemHierarchy($request, $this->menuItemEntity->childrenHierarchy($root), $menu);
        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }



    public function addMenuItemHierarchy($request, $children, $parent)
    {
        foreach ($children as $child) {
            $item = $this->addMenuItem($request, $child, $parent);
            if (!empty($child['__children'])) {
                $this->addMenuItemHierarchy($request, $child['__children'], $item);
            }
        }
    }


    /**
     * Utility method to convert MenuItem's from the doctrine model to Knp MenuItems
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Zicht\Bundle\MenuBundle\Entity\MenuItem $item
     * @param \Knp\Menu\MenuItem $menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function addMenuItem(Request $request, $item, \Knp\Menu\MenuItem $menu) {
        $attributes = array();

        if ($name = $item['name']) {
            $attributes['class'] = $name;
        }
        $baseUrl = $request->getBaseUrl();

        $menuItem = $menu->addChild(
            $item['title'],
            array(
                'uri' => $baseUrl . '/' . ltrim($item['path'], '/'),
                'attributes' => $attributes
            )
        );
        $menuItem->setExtras($item);
        return $menuItem;
    }


    /**
     * Adds an item on the fly that was not originally in the menu.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Knp\Menu\ItemInterface $item
     * @return void
     */
    public function addGhostItem(Request $request, $item, $title = '')
    {
        $item->addChild(
            $this->factory->createItem(
                $title,
                array(
                    'uri' => $request->getRequestUri(),
                    'display' => false
                )
            )
        );
    }
}