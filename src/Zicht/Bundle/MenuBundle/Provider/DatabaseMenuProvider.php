<?php
/**
 * @author Joppe Aarts <joppe@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zicht\Bundle\MenuBundle\Menu\Builder;
use Zicht\Bundle\MenuBundle\Menu\BuilderInterface;

/**
 * Class DatabaseMenuProvider
 *
 * @package Zicht\Bundle\BhicCoreBundle\Provider
 */
class DatabaseMenuProvider implements MenuProviderInterface
{
    /**
     * @var Builder
     */
    protected $builder = null;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param Builder $builder
     * @param ContainerInterface $container
     */
    public function __construct(BuilderInterface $builder, ContainerInterface $container)
    {
        $this->builder = $builder;
        $this->container = $container;
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
        return $this->builder->build($name, $this->container->get('request'));
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
        $root = $this->builder->hasRootItemByName($name, $this->container->get('request'));

        return null !== $root;
    }
}
