<?php
namespace AppBundle\Command;

use AppBundle\Service\STL\STLFileReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\STL\STLMillingEdit;

class STLEditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:edit')
            ->setDescription('Edit an STL file.')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Path to STL file.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $input->getArgument('filename');

        $stlFileReader = new STLFileReader(file_get_contents($fileName));
        $stlMillEditor = new STLMillingEdit($stlFileReader->toArray());
        $output->writeln($stlFileReader->setStlFileStringFromArray($stlMillEditor->extractMillingContent()->getStlFileContentArray())->getStlFileString());
    }
}