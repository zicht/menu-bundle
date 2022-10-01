<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetPreloadMenusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container
            ->getDefinition($container->getParameter('zicht_menu.builder_service'))
            ->addMethodCall('setPreloadMenus', [$container->getParameter('zicht_menu.preload_menus')]);
    }
}
