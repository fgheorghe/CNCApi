<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Service\STL;

class UploadStlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:upload')
            ->setDescription('Uploads an STL file.')
            ->addArgument('file', InputArgument::REQUIRED, 'Full path to STL file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');
        $stl->upload(file_get_contents($input->getArgument('file')));
    }
}