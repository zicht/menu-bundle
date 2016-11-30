<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ZichtMenuExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('admin.xml');

        if (!empty($config['preload_menus'])) {
            $container->getDefinition('zicht_menu.menu_builder')->addMethodCall('setPreloadMenus', [$config['preload_menus']]);
        }

        $formResources = $container->getParameter('twig.form.resources');
        $formResources[]= 'ZichtMenuBundle::form_theme.html.twig';
        $container->setParameter('twig.form.resources', $formResources);

        $container->getDefinition('zicht_menu.provider.database_menu_provider')->replaceArgument(0, new Reference($config['builder_service']));

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
