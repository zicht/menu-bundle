<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Menu;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Menu\FactoryInterface;

/**
 * @deprecated The aliasing is now delegated to a response listener in the UrlBundle. Extend the regular Builder in stead
 */
class UrlAliasingAwareBuilder extends Builder
{
    /**
     * Overridden to provide a DEPRECATED warning
     *
     * @param Registry $doctrine
     * @param string $entity
     */
    public function __construct(FactoryInterface $factory, $doctrine, $entity = 'ZichtMenuBundle:MenuItem')
    {
        parent::__construct($factory, $doctrine, $entity);

        trigger_error(
            'UrlAliasingAwareBuilder is deprecated: aliasing is now delegated to a response listener in the UrlBundle. '
            . 'Extend the regular Builder in stead',
            E_USER_DEPRECATED
        );
    }
}
