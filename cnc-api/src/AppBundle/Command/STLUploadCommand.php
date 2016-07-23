<?php
namespace AppBundle\Command;

use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\STL\STLMillingEdit;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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

        $stl = new STL(
            $this->getContainer(),
            new STLFileReader(file_get_contents($fileName)),
            $this->getContainer()->get('doctrine')->getManager()->getConnection(),
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            new AMQPStreamConnection(
                $this->getContainer()->getParameter('rabbit_mq_host'),
                $this->getContainer()->getParameter('rabbit_mq_port'),
                $this->getContainer()->getParameter('rabbit_mq_user'),
                $this->getContainer()->getParameter('rabbit_mq_password'),
                $this->getContainer()->getParameter('rabbit_mq_vhost')
            ),
            $this->getContainer()->getParameter('rabbit_mq_stl_queue_name'),
            $this->getContainer()->getParameter('rabbit_mq_stl_exchange_name')
        );

        try {
            $stl->upload();
            $output->writeln("File uploaded.");
        } catch (Exception $ex) {
            $output->writeln("Can not upload file: " . $ex->getMessage());
        }
    }
}