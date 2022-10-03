<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zicht\Bundle\MenuBundle\Menu\Builder;
use Zicht\Bundle\MenuBundle\Menu\BuilderInterface;

class DatabaseMenuProvider implements MenuProviderInterface
{
    /** @var Builder */
    protected $builder = null;

    /** @var RequestStack */
    protected $requestStack;

    /** @var MatcherInterface */
    protected $matcher;

    public function __construct(BuilderInterface $builder, RequestStack $requestStack, MatcherInterface $matcher)
    {
        $this->builder = $builder;
        $this->requestStack = $requestStack;
        $this->matcher = $matcher;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $menu = $this->builder->build($name, $this->requestStack->getCurrentRequest());

        foreach ($menu->getChildren() as $child) {
            $child->setCurrent($this->matcher->isCurrent($child));
        }

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        $root = $this->builder->hasRootItemByName($name, $this->requestStack->getCurrentRequest());

        return null !== $root;
    }
}
