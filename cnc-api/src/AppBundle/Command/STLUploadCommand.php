<?php
namespace AppBundle\Command;

use AppBundle\Service\STL\STLUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class STLUploadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:upload')
            ->setDescription('Upload an STL file.')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Path to STL file.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $input->getArgument('filename');

        try {
            (new STLUtil())->upload(file_get_contents($fileName), $this->getContainer());
            $output->writeln("File uploaded.");
        } catch (Exception $ex) {
            $output->writeln("Can not upload file: " . $ex->getMessage());
        }
    }
}