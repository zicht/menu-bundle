<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Twig;
 
use Knp\Menu\MenuItem;
use Knp\Menu\Provider\MenuProviderInterface;

/**
 * Class Extension
 *
 * @package Zicht\Bundle\MenuBundle\Twig
 */
class Extension extends \Twig_Extension
{
    /**
     * @var MenuProviderInterface
     */
    private $menuProvider;

    /**
     * Constructor
     *
     * @param MenuProviderInterface $menuProvider
     */
    public function __construct(MenuProviderInterface $menuProvider)
    {
        $this->menuProvider = $menuProvider;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'zicht_menu_active_trail' => new \Twig_Function_Method($this, 'menu_active_trail'),
            'zicht_menu_exists' => new \Twig_Function_Method($this, 'menuExists')
        );
    }


    /**
     * Returns if the given menuName exists
     *
     * @param string $menuName
     * @return bool
     */
    public function menuExists($menuName)
    {
        return $this->menuProvider->has($menuName);
    }

    /**
     * Returns the active trail for the given menuItem
     *
     * @param \Knp\Menu\MenuItem $item
     * @return array
     *
     */
    public function menuActiveTrail(MenuItem $item)
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