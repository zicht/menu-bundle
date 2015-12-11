<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Twig;
 
class Extension extends \Twig_Extension
{
    public function __construct()
    {
    }

    public function getFunctions()
    {
        return array(
            'zicht_menu_active_trail' => new \Twig_Function_Method($this, 'menu_active_trail')
        );
    }


    public function menu_active_trail(\Knp\Menu\MenuItem $item)
    {
        $stack = array();
        do {
            $stack[]= $item;
        } while ($item = $item->getParent());
        return array_reverse($stack);
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'zicht_menu';
    }
}