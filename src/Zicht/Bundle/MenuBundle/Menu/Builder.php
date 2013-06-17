<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;

class Builder extends ContainerAware
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Gedmo\Tree\Entity\Repository\NestedTreeRepository
     */
    private $menuItemEntity;


    /**
     * @param FactoryInterface $factory
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(FactoryInterface $factory, $doctrine, $entity = 'ZichtMenuBundle:MenuItem')
    {
        $this->factory = $factory;
        $this->menuItemEntity = $doctrine->getManager()->getRepository($entity);;
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
            'path' => null,
            'name' => $name
        ));

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

        return $menuItem;
    }
}