<?php
/**
 * @author Muhammed Akbulut <muhammed@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ZichtMenuBundle
 *
 * @package Zicht\Bundle\MenuBundle
 */
class ZichtMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DependencyInjection\CompilerPass\SetPreloadMenusPass());
    }
}
