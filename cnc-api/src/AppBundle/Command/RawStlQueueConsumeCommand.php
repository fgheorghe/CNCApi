<?php
namespace AppBundle\Command;

use php3d\stlslice\Examples\STLMillingEdit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\STL;
use php3d\stl\STL as STLObject;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use \Exception;

class RawStlQueueConsumeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:queue:raw:consume')
            ->setDescription('Consumes the RAW STL queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');
        $container = $this->getContainer();
        $queue = $this->getContainer()->getParameter('rabbit_mq_raw_stl_queue_name');
        $exchange = $this->getContainer()->getParameter('rabbit_mq_raw_exchange_name');
        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_consumer.php
        $connection = new AMQPStreamConnection(
            $this->getContainer()->getParameter('rabbit_mq_host'),
            $this->getContainer()->getParameter('rabbit_mq_port'),
            $this->getContainer()->getParameter('rabbit_mq_user'),
            $this->getContainer()->getParameter('rabbit_mq_password'),
            $this->getContainer()->getParameter('rabbit_mq_vhost')
        );

        $channel = $connection->channel();
        $channel->queue_declare($this->getContainer()->getParameter('rabbit_mq_raw_stl_queue_name'), false, true, false,
            false);
        $channel->exchange_declare($this->getContainer()->getParameter('rabbit_mq_raw_exchange_name'), 'direct', false,
            true, false);
        $channel->queue_bind($this->getContainer()->getParameter('rabbit_mq_raw_stl_queue_name'),
            $this->getContainer()->getParameter('rabbit_mq_raw_exchange_name'));
        $channel->basic_consume($this->getContainer()->getParameter('rabbit_mq_raw_stl_queue_name'),
            'api-stl-queue-consumer', false, false, false, false,
            function (AMQPMessage $message) use ($container, $stl, $connection, $exchange, $queue, $output) {
                $stlObject = STLObject::fromString($message->body);
                $output->writeln("Processing STL object: " . $stlObject->getSolidName());
                $id = $stl->getId($stlObject->getSolidName());

                $millingEditor = new STLMillingEdit($stlObject->toArray());
                $millingEditor->extractMillingContent();
                $processedStl = STLObject::fromArray($millingEditor->getStlFileContentArray());

                $stl->set($id, "stl_object_mill_data", $processedStl->toString());
                $stl->set($id, "stl_object_mill_coordinates", json_encode($processedStl->toArray()));
                $stl->set($id, "stl_object_status", "milling");

                $queueName = $container->getParameter('rabbit_mq_mill_queue_name');
                $exchangeName = $container->getParameter('rabbit_mq_mill_exchange_name');

                $channel = $connection->channel();
                // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
                $channel->queue_declare($queueName, false, true, false, false);
                $channel->exchange_declare($exchangeName, 'direct', false, true, false);
                $channel->queue_bind($queueName, $exchangeName);
                $channel->basic_publish(new AMQPMessage(
                    $processedStl->toString(),
                    array(
                        'content_type' => 'text/plain',
                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                    )
                ), $exchangeName);
                $channel->close();

                try {
                    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                    $output->writeln("Processed STL object: " . $stlObject->getSolidName());
                } catch (Exception $ex) {
                    $output->writeln("Can not process queued STL object (" . $stlObject->getSolidName() . "): " . $ex->getMessage());
                }
            });
        register_shutdown_function(function () use ($channel, $connection) {
            $channel->close();
            $connection->close();
        }, $channel, $connection);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}