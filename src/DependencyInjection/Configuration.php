<?php
/**
 * @copyright Zicht Online <https://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Config for menu bundle.
 */
class Configuration implements ConfigurationInterface
{
    /** {@inheritDoc} */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('zicht_menu');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('builder_service')->defaultValue('zicht_menu.menu_builder')->end()
                ->arrayNode('menus')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
