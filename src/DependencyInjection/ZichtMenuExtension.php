<?php
/**
 * @copyright Zicht online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ZichtMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('zicht_menu.builder_service', $config['builder_service']);
        $container->setParameter('zicht_menu.preload_menus', $config['menus']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('admin.xml');

        $formResources = $container->getParameter('twig.form.resources');
        $formResources[] = '@ZichtMenu/form_theme.html.twig';
        $container->setParameter('twig.form.resources', $formResources);

        $container->getDefinition('zicht_menu.provider.database_menu_provider')->replaceArgument(0, new Reference($config['builder_service']));
        if ($config['builder_service'] !== 'zicht_menu.menu_builder') {
            $container->removeDefinition('zicht_menu.menu_builder');
            $container->setAlias('zicht_menu.menu_builder', $config['builder_service']);
        }

        // knp menu ^2:
        if (interface_exists('Knp\Menu\Matcher\Voter\VoterInterface')) {
            $def = new Definition('Zicht\Bundle\MenuBundle\Voter\UriVoter');
            $def->addTag('knp_menu.voter', ['request' => true]);
            $def->addArgument(new Reference('request_stack'));
            $container->setDefinition('zicht_menu.knp_menu.voter.uri', $def);

            $container->getDefinition('zicht_menu.twig.extension')
                ->addMethodCall('setMatcher', [new Reference('knp_menu.matcher')]);
        }
    }
}
