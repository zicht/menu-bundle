<?php
/**
 * @copyright Zicht Online <https://zicht.nl>
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

    public function getGroup()
    {
        return 'Menu bundle';
    }

    public function getOrder()
    {
        return 0;
    }

    public function getName()
    {
        return 'Nested tree status';
    }

    public function getDescription()
    {
        return sprintf('Check that the menu stored in entity "%s" is valid', MenuItem::class);
    }
}
