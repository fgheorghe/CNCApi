<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Service\STL;

class SetStlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:set')
            ->setDescription('Sets a given STL file database column data.')
            ->addArgument('id', InputArgument::REQUIRED, 'STL File ID.')
            ->addArgument('column', InputArgument::REQUIRED, 'Database column name.')
            ->addArgument('value', InputArgument::REQUIRED, 'Database column value.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');

        $stl->set($input->getArgument('id'), $input->getArgument('column'), $input->getArgument('value'));
    }
}