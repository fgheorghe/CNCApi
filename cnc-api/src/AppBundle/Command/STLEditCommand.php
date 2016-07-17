<?php
// src/AppBundle/Command/GreetCommand.php
namespace AppBundle\Command;

use AppBundle\Service\STL\STLFileReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class STLEditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:edit')
            ->setDescription('Edit and STL file.')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Path to STL file.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: Implement.
    }
}