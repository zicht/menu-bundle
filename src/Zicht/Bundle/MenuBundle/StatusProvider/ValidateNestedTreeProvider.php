<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\StatusProvider;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;
use Zicht\Bundle\StatusBundle\StatusProvider\StatusProviderHelper;
use Zicht\Bundle\StatusBundle\StatusProvider\StatusProviderInterface;

/**
 * Class ValidateNestedTreeProvider
 *
 * @package Zicht\Bundle\MenuBundle\StatusProvider
 */
class ValidateNestedTreeProvider extends StatusProviderHelper implements StatusProviderInterface
{
    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * ValidateNestedTreeProvider constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @{inheritDoc}
     */
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
