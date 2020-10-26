<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Provider;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zicht\Bundle\MenuBundle\Menu\Builder;
use Zicht\Bundle\MenuBundle\Menu\BuilderInterface;

class DatabaseMenuProvider implements MenuProviderInterface
{
    /**
     * @var Builder
     */
    protected $builder = null;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var MatcherInterface
     */
    protected $matcher;

    public function __construct(BuilderInterface $builder, RequestStack $requestStack, MatcherInterface $matcher = null)
    {
        $this->builder = $builder;
        $this->requestStack = $requestStack;
        $this->matcher = $matcher;
    }

    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
        $menu = $this->builder->build($name, $this->requestStack->getCurrentRequest());

        if ($this->matcher !== null) {
            foreach ($menu->getChildren() as $child) {
                $child->setCurrent($this->matcher->isCurrent($child));
            }
        }

        return $menu;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        $root = $this->builder->hasRootItemByName($name, $this->requestStack->getCurrentRequest());

        return null !== $root;
    }
}
