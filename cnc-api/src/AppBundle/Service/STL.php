<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\DBAL\Connection;
use Symfony\Component\Config\Definition\Exception\Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class STL
 * @package AppBundle\Service
 */
class STL {
    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    public function getContainer() : Container
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return STL
     */
    private function setContainer(Container $container) : STL
    {
        $this->container = $container;
        return $this;
    }

    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * Upload an STL file, and kick start processing.
     *
     * @param string $fileContent
     */
    public function upload(string $fileContent)
    {
        $stl = \php3d\stl\STL::fromString($fileContent);

        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        $statement = $databaseConnection->prepare("INSERT INTO 
            stl_objects 
        SET 
            stl_object_name = :stl_object_name,
            stl_object_status = 'raw',
            stl_object_data = :stl_object_data,
            stl_object_coordinates = :stl_object_coordinates
        ");

        $statement->execute(array(
            "stl_object_name" => $stl->getSolidName(),
            "stl_object_data" => $fileContent,
            "stl_object_coordinates" => json_encode($stl->toArray())
        ));

        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
        $queueConnection = new AMQPStreamConnection(
            $this->getContainer()->getParameter('rabbit_mq_host'),
            $this->getContainer()->getParameter('rabbit_mq_port'),
            $this->getContainer()->getParameter('rabbit_mq_user'),
            $this->getContainer()->getParameter('rabbit_mq_password'),
            $this->getContainer()->getParameter('rabbit_mq_vhost')
        );

        $queueName = $this->getContainer()->getParameter('rabbit_mq_raw_stl_queue_name');
        $exchangeName = $this->getContainer()->getParameter('rabbit_mq_raw_exchange_name');

        $channel = $queueConnection->channel();
        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->exchange_declare($exchangeName, 'direct', false, true, false);
        $channel->queue_bind($queueName, $exchangeName);
        $channel->basic_publish(new AMQPMessage(
            $fileContent,
            array(
                'content_type' => 'text/plain',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            )
        ), $exchangeName);
        $channel->close();
    }

    /**
     * Lists uploaded STL files.
     *
     * @return array
     */
    public function listStlFiles() : array
    {
        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        return $databaseConnection->fetchAll("SELECT stl_object_id, stl_object_name, stl_object_status FROM stl_objects ORDER BY stl_object_id ASC");
    }

    /**
     * Fetches an object id by name.
     *
     * @param string $name
     * @return int
     */
    public function getId(string $name) : int
    {
        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        return $databaseConnection->fetchColumn("SELECT stl_object_id FROM stl_objects WHERE stl_object_name = :stl_object_name LIMIT 1", array(
            "stl_object_name" => $name,
            0
        ));
    }

    /**
     * Fetches the content of a given column for a given STL file, by id.
     *
     * @param int $id
     * @param string $column
     * @return string
     */
    public function get(int $id, string $column) : string
    {
        if (!in_array(strtolower($column), array("stl_object_name", "stl_object_status", "stl_object_data", "stl_object_coordinates", "stl_object_mill_data", "stl_object_mill_coordinates", "stl_object_gcode_data")))
        {
            throw new Exception("Invalid column name.");
        }

        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        return $databaseConnection->fetchColumn("SELECT " . $column . " FROM stl_objects WHERE stl_object_id = :stl_object_id LIMIT 1", array(
            "stl_object_id" => $id,
            0
        ));
    }

    /**
     * Sets an STL object database value by id and a given column name.
     *
     * @param int $id
     * @param string $column
     * @param string $value
     */
    public function set(int $id, string $column, string $value)
    {
        if (!in_array(strtolower($column), array("stl_object_name", "stl_object_status", "stl_object_data", "stl_object_coordinates", "stl_object_mill_data", "stl_object_mill_coordinates", "stl_object_gcode_data")))
        {
            throw new Exception("Invalid column name.");
        }

        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        $databaseConnection->executeQuery("UPDATE stl_objects SET " . $column . " = :column_value WHERE stl_object_id = :stl_object_id LIMIT 1",
            array(
                "column_value" => $value,
                "stl_object_id" => $id
            )
        );
    }
}