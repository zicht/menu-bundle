<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\StatusProvider;

use Doctrine\Persistence\ManagerRegistry;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;
use Zicht\Bundle\StatusBundle\StatusProvider\StatusProviderInterface;

/**
 * Class ValidateNestedTreeProvider
 *
 * @package Zicht\Bundle\MenuBundle\StatusProvider
 */
class ValidateNestedTreeProvider implements StatusProviderInterface
{
    /** @var boolean */
    protected $isValid;

    /** @var array */
    protected $values;

    /**
     * ValidateNestedTreeProvider constructor.
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(MenuItem::class);
        $result = $repository->verify();

        if (true === $result) {
            $this->isValid = true;
            $this->values = ['error_count' => 0];
        } else {
            $this->isValid = false;
            $this->values = ['error_count' => sizeof($result)];
        }
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

    /**
     * @{inheritDoc}
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @{inheritDoc}
     */
    public function isValid()
    {
        return $this->isValid;
    }
}
