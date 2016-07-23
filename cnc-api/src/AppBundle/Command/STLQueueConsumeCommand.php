<?php
namespace AppBundle\Command;

use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Exception;

class STLQueueConsumeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:queue:consume')
            ->setDescription('Consume STL queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $database = $this->getContainer()->get('doctrine')->getManager()->getConnection();
        $queue = $this->getContainer()->getParameter('rabbit_mq_gcode_queue_name');
        $exchange = $this->getContainer()->getParameter('rabbit_mq_gcode_exchange_name');

        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_consumer.php
        $connection = new AMQPStreamConnection(
            $this->getContainer()->getParameter('rabbit_mq_host'),
            $this->getContainer()->getParameter('rabbit_mq_port'),
            $this->getContainer()->getParameter('rabbit_mq_user'),
            $this->getContainer()->getParameter('rabbit_mq_password'),
            $this->getContainer()->getParameter('rabbit_mq_vhost')
        );
        $channel = $connection->channel();
        $channel->queue_declare($this->getContainer()->getParameter('rabbit_mq_stl_queue_name'), false, true, false, false);
        $channel->exchange_declare($this->getContainer()->getParameter('rabbit_mq_stl_exchange_name'), 'direct', false, true, false);
        $channel->queue_bind($this->getContainer()->getParameter('rabbit_mq_stl_queue_name'), $this->getContainer()->getParameter('rabbit_mq_stl_exchange_name'));
        $channel->basic_consume($this->getContainer()->getParameter('rabbit_mq_stl_queue_name'), 'api-stl-queue-consumer', false, false, false, false, function($message) use ($container, $database, $connection, $exchange, $queue, $output) {
            $stl = new STL(
                $container,
                new STLFileReader($message->body),
                $database,
                new AMQPStreamConnection(
                    $this->getContainer()->getParameter('rabbit_mq_host'),
                    $this->getContainer()->getParameter('rabbit_mq_port'),
                    $this->getContainer()->getParameter('rabbit_mq_user'),
                    $this->getContainer()->getParameter('rabbit_mq_password'),
                    $this->getContainer()->getParameter('rabbit_mq_vhost')
                ),
                $queue,
                $exchange
            );
            $output->writeln("Processing STL object: " . $stl->getStlFileReader()->getName());

            try {
                $stl->unpack();
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                $output->writeln("Processed STL object: " . $stl->getStlFileReader()->getName());
            } catch (Exception $ex) {
                $output->writeln("Can not process queued STL object (" . $stl->getStlFileReader()->getName() . "): " . $ex->getMessage());
            }
        });

        register_shutdown_function(function() use ($channel, $connection) {
            $channel->close();
            $connection->close();
        }, $channel, $connection);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}