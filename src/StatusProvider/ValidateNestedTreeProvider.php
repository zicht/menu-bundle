<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\StatusProvider;

use Doctrine\Persistence\ManagerRegistry;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;
use Zicht\Bundle\StatusBundle\StatusProvider\StatusProviderHelper;
use Zicht\Bundle\StatusBundle\StatusProvider\StatusProviderInterface;

class ValidateNestedTreeProvider extends StatusProviderHelper implements StatusProviderInterface
{
    /** @var ManagerRegistry */
    private $doctrine;

    /**
     * ValidateNestedTreeProvider constructor.
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function check()
    {
        $repository = $this->doctrine->getRepository(MenuItem::class);
        $result = $repository->verify();

        if (true === $result) {
            return [true, ['error_count' => 0]];
        }
        return [false, ['error_count' => count($result)]];
    }

    /**
     * @{inheritDoc}
     */
    public function getGroup()
    {
        return 'Menu bundle';
    }

    /**
     * @{inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'Nested tree status';
    }

    /**
     * @{inheritDoc}
     */
    public function getDescription()
    {
        return sprintf('Check that the menu stored in entity "%s" is valid', MenuItem::class);
    }
}
