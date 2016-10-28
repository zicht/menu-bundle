<?php
/**
 * @author Muhammed Akbulut <muhammed@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Config for menu bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zicht_menu');

        $rootNode
            ->children()
                ->scalarNode('builder_service')->defaultValue('zicht_menu.menu_builder')->end()
                ->arrayNode('menus')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('preload_menus')->prototype('scalar')->end()->defaultValue(['service', 'main', 'footer'])->end()
            ->end();

        return $treeBuilder;
    }
}
