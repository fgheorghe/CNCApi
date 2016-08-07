<?php
namespace AppBundle\Command;

use php3d\stlslice\Examples\STL2GCode;
use php3d\stlslice\STLSlice;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\STL;
use php3d\stl\STL as STLObject;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use \Exception;

class MillStlQueueConsumeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stl:queue:mill:consume')
            ->setDescription('Consumes the MILL processed STL queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var STL $stl
         */
        $stl = $this->getContainer()->get('stl');
        $container = $this->getContainer();
        $queue = $this->getContainer()->getParameter('rabbit_mq_mill_queue_name');
        $exchange = $this->getContainer()->getParameter('rabbit_mq_mill_exchange_name');
        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_consumer.php
        $connection = new AMQPStreamConnection(
            $this->getContainer()->getParameter('rabbit_mq_host'),
            $this->getContainer()->getParameter('rabbit_mq_port'),
            $this->getContainer()->getParameter('rabbit_mq_user'),
            $this->getContainer()->getParameter('rabbit_mq_password'),
            $this->getContainer()->getParameter('rabbit_mq_vhost')
        );

        $channel = $connection->channel();
        $channel->queue_declare($this->getContainer()->getParameter('rabbit_mq_mill_queue_name'), false, true, false,
            false);
        $channel->exchange_declare($this->getContainer()->getParameter('rabbit_mq_mill_exchange_name'), 'direct', false,
            true, false);
        $channel->queue_bind($this->getContainer()->getParameter('rabbit_mq_mill_queue_name'),
            $this->getContainer()->getParameter('rabbit_mq_mill_exchange_name'));
        $channel->basic_consume($this->getContainer()->getParameter('rabbit_mq_mill_queue_name'),
            'api-stl-queue-consumer', false, false, false, false,
            function (AMQPMessage $message) use ($container, $stl, $connection, $exchange, $queue, $output) {
                $stlObject = STLObject::fromString($message->body);
                $output->writeln("Processing STL object: " . $stlObject->getSolidName());
                $id = $stl->getId($stlObject->getSolidName());

                $layers = (new STLSlice($stlObject, 10))->slice();
                $gcode = (new STL2GCode($layers, 100))->toGCodeString();

                $stl->set($id, "stl_object_gcode_data", $gcode);
                $stl->set($id, "stl_object_status", "gcode");

                $queueName = $container->getParameter('rabbit_mq_gcode_queue_name');
                $exchangeName = $container->getParameter('rabbit_mq_gcode_exchange_name');

                $channel = $connection->channel();
                // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
                $channel->queue_declare($queueName, false, true, false, false);
                $channel->exchange_declare($exchangeName, 'direct', false, true, false);
                $channel->queue_bind($queueName, $exchangeName);
                $channel->basic_publish(new AMQPMessage(
                    $gcode,
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