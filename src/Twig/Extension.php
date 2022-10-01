<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Twig;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\MenuItem;
use Knp\Menu\Provider\MenuProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    /** @var MenuProviderInterface */
    private $menuProvider;

    /** @var MatcherInterface */
    private $matcher;

    public function __construct(MenuProviderInterface $menuProvider)
    {
        $this->menuProvider = $menuProvider;
    }

    /**
     * @param MatcherInterface $matcher
     */
    public function setMatcher($matcher = null)
    {
        $this->matcher = $matcher;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('zicht_menu_current', [$this, 'current']),
            new TwigFilter('zicht_menu_active_trail', [$this, 'activeTrail']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'zicht_menu_active_trail' => new TwigFunction('zicht_menu_active_trail', [$this, 'activeTrail']),
            'zicht_menu_exists' => new TwigFunction('zicht_menu_exists', [$this, 'exists']),
        ];
    }

    /**
     * @param string $menuName
     * @return bool
     */
    public function exists($menuName)
    {
        return $this->menuProvider->has($menuName);
    }

    /**
     * @param MenuItem|null $item
     * @return array
     */
    public function activeTrail($item)
    {
        if (is_null($item)) {
            return null;
        }

        if (!$item instanceof MenuItem) {
            throw new \UnexpectedValueException(sprintf('$ITEM must be \Knp\Menu\MenuItem not "%s"', get_class($item)));
        }

        $stack = [];

        do {
            $stack[] = $item;
        } while ($item = $item->getParent());

        return array_reverse($stack);
    }

    /**
     * @param MenuItem|null $item
     * @param int $level
     * @return MenuItem|null
     */
    public function current($item, $level = null)
    {
        if (is_null($item)) {
            return null;
        }

        if (!$item instanceof MenuItem) {
            throw new \UnexpectedValueException(sprintf('$ITEM must be \Knp\Menu\MenuItem not "%s"', get_class($item)));
        }

        /** @var MenuItem $child */
        foreach ($item->getChildren() as $child) {
            if (
                (null !== $this->matcher && $this->matcher->isAncestor($child))
                || (null === $this->matcher && $child->isCurrentAncestor())
            ) {
                if ($level !== null and $level == $child->getLevel()) {
                    return $child;
                }

                return $this->current($child);
            }

            if (
                (null !== $this->matcher && $this->matcher->isCurrent($child))
                || (null === $this->matcher && $child->isCurrent())
            ) {
                return $child;
            }
        }

        return null;
    }
}
