<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 13/07/16
 * Time: 22:39
 */

namespace AppBundle\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STLFileReader;
use AppBundle\Service\STL\STLMillingEdit;
use Doctrine\DBAL\Connection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class STL. Provides STL conversion functionality.
 * @package AppBundle\Service\STL
 */
class STL
{
    private $container;
    private $stlFileReader;
    private $stlMillingEditor;
    private $databaseConnection;
    private $queueConnection;
    private $queueName;
    private $exchangeName;

    /**
     * @return mixed
     */
    public function getExchangeName() : string
    {
        return $this->exchangeName;
    }

    /**
     * @param mixed $exchangeName
     * @return STL
     */
    public function setExchangeName(string $exchangeName) : STL
    {
        $this->exchangeName = $exchangeName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueueName() : string
    {
        return $this->queueName;
    }

    /**
     * @param mixed $queueName
     * @return STL
     */
    public function setQueueName(string $queueName) : STL
    {
        $this->queueName = $queueName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueueConnection() : AMQPStreamConnection
    {
        return $this->queueConnection;
    }

    /**
     * @param mixed $queueConnection
     * @return STL
     */
    public function setQueueConnection($queueConnection) : STL
    {
        $this->queueConnection = $queueConnection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatabaseConnection() : Connection
    {
        return $this->databaseConnection;
    }

    /**
     * @param mixed $databaseConnection
     * @return STL
     */
    public function setDatabaseConnection(Connection $databaseConnection) : STL
    {
        $this->databaseConnection = $databaseConnection;
        return $this;
    }

    /**
     * @return STLMillingEdit
     */
    public function getStlMillingEditor() : STLMillingEdit
    {
        return $this->stlMillingEditor;
    }

    /**
     * @param mixed $stlEditor
     * @return STL
     */
    public function setStlMillingEditor(STLMillingEdit $stlEditor) : STL
    {
        $this->stlMillingEditor = $stlEditor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStlFileReader() : STLFileReader
    {
        return $this->stlFileReader;
    }

    /**
     * @param mixed $stlFileReader
     * @return STL
     */
    public function setStlFileReader(STLFileReader $stlFileReader)
    {
        $this->stlFileReader = $stlFileReader;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getContainer() : Container
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return STL
     */
    protected function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * STL constructor.
     * @param Container $container
     * @param \AppBundle\Service\STL\STLFileReader $stlFileReader
     * @param Connection $databaseConnection
     * @param String $queueName
     * @param String $exchangeName
     */
    public function __construct(
        Container $container,
        STLFileReader $stlFileReader,
        Connection $databaseConnection,
        AMQPStreamConnection $queueConnection,
        string $queueName,
        string $exchangeName
    ) {
        $this->setContainer($container);
        $this->setStlFileReader($stlFileReader);
        $this->setDatabaseConnection($databaseConnection);
        $this->setQueueConnection($queueConnection);
        $this->setQueueName($queueName);
        $this->setExchangeName($exchangeName);
    }

    /**
     * Uploads an STL file to the database, and pushes it to a queue.
     */
    public function upload()
    {
        $name = $this->getStlFileReader()->getName();
        $data = $this->getStlFileReader()->getStlFileString();

        $this->getDatabaseConnection()
            ->executeQuery(
                "INSERT INTO stl_objects SET stl_object_name = :name, stl_object_status = :status, stl_object_data = :data",
                array(
                    "name" => $name,
                    "status" => "raw",
                    "data" => $data
                )
            );

        $channel = $this->getQueueConnection()->channel();
        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->exchange_declare($this->getExchangeName(), 'direct', false, true, false);
        $channel->queue_bind($this->getQueueName(), $this->getExchangeName());
        $channel->basic_publish(new AMQPMessage(
            $data,
            array(
                'content_type' => 'text/plain',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            )
        ), $this->getExchangeName());

        $channel->close();
    }

    /**
     * Extracts coordinates and saves them in the database as a JSON object.
     */
    public function unpack() {
        $millingEdit = new STLMillingEdit($this->getStlFileReader()->toArray());
        $name = $this->getStlFileReader()->getName();
        $coordinates = $millingEdit->extractMillingContent()->getStlFileContentArray();

        $this->getDatabaseConnection()
            ->executeQuery(
                "UPDATE stl_objects SET stl_object_status = :status, stl_object_coordinates = :coordinates WHERE stl_object_name = :name LIMIT 1",
                array(
                    "status" => "coordinates",
                    "coordinates" => json_encode($coordinates),
                    "name" => $name
                )
            );

        $channel = $this->getQueueConnection()->channel();
        // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->exchange_declare($this->getExchangeName(), 'direct', false, true, false);
        $channel->queue_bind($this->getQueueName(), $this->getExchangeName());
        $channel->basic_publish(new AMQPMessage(
            json_encode($coordinates),
            array(
                'content_type' => 'text/plain',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            )
        ), $this->getExchangeName());

        $channel->close();
    }
}