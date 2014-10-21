<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Command;

use Doctrine\ORM\EntityManager;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class FixMenuCommand
 *
 * @package Zicht\Bundle\MenuBundle\Command
 */
class FixMenuCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zicht:menu:repair')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'do a dry run to check')
            ->setDescription('Will repair broke menu tree')
            ->setHelp(<<<EOF

    This command will try to repair broken menu, do a dry-run
    to see if menu is broken (use verbose to see which items are broken)

EOF
    )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em         = $this->getContainer()->get('doctrine')->getManager();
        /** @var NestedTreeRepository $menu */
        $menu      = $em->getRepository('ZichtMenuBundle:MenuItem');
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');
        $dryRun    = $input->getOption('dry-run');
        $verbose   = (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity());

        if (true !== $result = $menu->verify()) {
            if ($dryRun) {
                if ($verbose) {
                    $output->writeln($formatter->formatBlock($result, 'comment', true));
                } else {
                    $output->writeln(sprintf('Found <info>%s</info> errors in menu', count($result)));
                }
            } else {
                $menu->recover();
                $em->flush();
                $output->writeln('Finished recovering menu');

            }
        } else {
            $output->writeln('No errors found while verifying the menu');
        }
    }
}