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

        if (!empty($config['menus'])) {
            $service = new Definition('Knp\Menu\MenuItem');
            $service->setScope('request');
            $service->setFactoryMethod('build');
            $service->setFactoryService($config['builder_service']);
            $service->addArgument(null);
            $service->addArgument(new Reference('request'));
            foreach ($config['menus'] as $menuId) {
                $instance = clone $service;
                $instance->replaceArgument(0, $menuId);
                $instance->addTag('knp_menu.menu', array('alias' => $menuId));
                $container->setDefinition('zicht_menu.menus.' . $menuId, $instance);
            }
        }

        $formResources = $container->getParameter('twig.form.resources');
        $formResources[]= 'ZichtMenuBundle::form_theme.html.twig';
        $container->setParameter('twig.form.resources', $formResources);
    }

}
