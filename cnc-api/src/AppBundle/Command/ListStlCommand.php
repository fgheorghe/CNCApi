<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\STL;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListStlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:list')
            ->setDescription('Lists STL files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');
        $io = new SymfonyStyle($input, $output);

        $io->table(
            array('STL Object ID', 'STL Object Name', 'STL Object Status'),
            $stl->listStlFiles()
        );
    }
}