<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Menu;

use Doctrine\Persistence\ManagerRegistry;
use Knp\Menu\FactoryInterface;
use Zicht\Bundle\MenuBundle\Entity\MenuItem as MenuItemEntity;

/**
 * @deprecated The aliasing is now delegated to a response listener in the UrlBundle. Extend the regular Builder in stead
 */
class UrlAliasingAwareBuilder extends Builder
{
    /**
     * Overridden to provide a DEPRECATED warning
     *
     * @param class-string $entity
     */
    public function __construct(FactoryInterface $factory, ManagerRegistry $doctrine, $entity = MenuItemEntity::class)
    {
        parent::__construct($factory, $doctrine, $entity);

        trigger_error(
            'UrlAliasingAwareBuilder is deprecated: aliasing is now delegated to a response listener in the UrlBundle. '
            . 'Extend the regular Builder in stead',
            E_USER_DEPRECATED
        );
    }
}
