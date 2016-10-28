<?php
/**
 * @author Muhammed Akbulut <muhammed@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zicht\Bundle\MenuBundle\DependencyInjection\CompilerPass\ReplaceMenuBuilderServicePass;

/**
 * Class ZichtMenuBundle
 *
 * @package Zicht\Bundle\MenuBundle
 */
class ZichtMenuBundle extends Bundle
{
    /**
     * @{inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ReplaceMenuBuilderServicePass());
    }
}
