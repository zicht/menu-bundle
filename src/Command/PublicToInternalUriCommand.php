<?php

/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command class for replacing public menu URI’s with internal ones.
 */
class PublicToInternalUriCommand extends Command
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected static $defaultName = 'zicht:menu:public-to-internal';

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Convert public menu URI’s to internal URI’s');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->getConnection()->exec(
                'UPDATE menu_item ' .
                'INNER JOIN url_alias ON menu_item.path = url_alias.public_url ' .
                'SET menu_item.path = url_alias.internal_url'
            );

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $exception) {
            $this->entityManager->getConnection()->rollBack();

            throw $exception;
        }

        return Command::SUCCESS;
    }
}
