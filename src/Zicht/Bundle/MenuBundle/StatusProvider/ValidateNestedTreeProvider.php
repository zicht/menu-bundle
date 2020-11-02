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

class ValidateNestedTreeProvider extends StatusProviderHelper implements StatusProviderInterface
{
    /** @var boolean */
    protected $isValid;

    /** @var array */
    protected $values;

    /** @var ManagerRegistry */
    private $doctrine;

    /**
     * ValidateNestedTreeProvider constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function check()
    {
        $repository = $this->doctrine->getRepository(MenuItem::class);
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
