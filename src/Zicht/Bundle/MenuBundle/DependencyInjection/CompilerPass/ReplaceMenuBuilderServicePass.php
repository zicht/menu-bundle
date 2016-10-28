<?php
/**
 * @author Rik van der Kemp <rik@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ReplaceMenuBuilderServicePass.
 */
class ReplaceMenuBuilderServicePass implements CompilerPassInterface
{
    /**
     * @{inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('zicht_menu.menu_builder.service')) {
            return;
        }

        $builderService = $container->getParameter('zicht_menu.menu_builder.service');
        $def = $container->getDefinition('zicht_menu.provider.database_menu_provider');
        $def->replaceArgument(0, new Reference($builderService));
    }
}
