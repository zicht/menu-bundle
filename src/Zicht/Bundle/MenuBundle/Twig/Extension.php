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

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('zicht_menu_current', [$this, 'zicht_menu_current']),
            new \Twig_SimpleFilter('zicht_menu_active_trail', [$this, 'zicht_menu_active_trail']),
        ];
    }
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'zicht_menu_active_trail' => new \Twig_Function_Method($this, 'zicht_menu_active_trail'),
            'zicht_menu_exists' => new \Twig_Function_Method($this, 'zicht_menu_exists')
        );
    }

    /**
     * Returns if the given menuName exists
     *
     * @param string $menuName
     * @return bool
     */
    public function zicht_menu_exists($menuName)
    {
        return $this->menuProvider->has($menuName);
    }

    /**
     * Returns the active trail for the given menuItem
     *
     * @param MenuItem|null $item
     * @return array
     *
     */
    public function zicht_menu_active_trail($item)
    {
        if (is_null($item)) {
            return null;
        }

        if (!$item instanceof MenuItem) {
            throw new \UnexpectedValueException(sprintf('$ITEM must be \Knp\Menu\MenuItem not "%s"', get_class($item)));
        }

        $stack = array();
        do {
            $stack[]= $item;
        } while ($item = $item->getParent());
        return array_reverse($stack);
    }

    /**
     * Returns the current menu item given a root menu item
     *
     * @param MenuItem|null $item
     * @return MenuItem|null
     */
    public function zicht_menu_current($item, $level = null)
    {
        if (is_null($item)) {
            return null;
        }

        if (!$item instanceof MenuItem) {
            throw new \UnexpectedValueException(sprintf('$ITEM must be \Knp\Menu\MenuItem not "%s"', get_class($item)));
        }

        /** @var MenuItem $child */
        foreach ($item->getChildren() as $child) {
            if ($child->isCurrentAncestor()) {
                if ($level !== null and $level == $child->getLevel()) {
                    return $child;
                }
                return $this->zicht_menu_current($child);
            }

            if ($child->isCurrent()) {
                return $child;
            }
        }

        return null;
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