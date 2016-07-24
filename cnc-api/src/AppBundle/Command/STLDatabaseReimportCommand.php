<?php
namespace AppBundle\Command;

use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use AppBundle\Service\STL\STLUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Exception;

class STLDatabaseReimportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:reimport')
            ->setDescription('Consume STL queue.')
            ->addArgument(
                "name",
                InputArgument::REQUIRED,
                "STL object name."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $database = $this->getContainer()->get('doctrine');
        $name = $input->getArgument("name");

        $output->writeln("Processing STL object: " . $name);

        $content = (new STLUtil())->getStlData($name, $database);

        try {
            (new STLUtil())->upload($content, $container, true);
            $output->writeln("Processed STL object: " . $name);
        } catch (Exception $ex) {
            $output->writeln("Can not upload file: " . $ex->getMessage());
        }
    }
}