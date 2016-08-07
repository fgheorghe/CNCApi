<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Service\STL;

class GetStlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:get')
            ->setDescription('Fetches a give STL file database column data.')
            ->addArgument('id', InputArgument::REQUIRED, 'STL File ID.')
            ->addArgument('column', InputArgument::REQUIRED, 'Database column name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');

        $output->writeln($stl->get($input->getArgument('id'), $input->getArgument('column')));
    }
}