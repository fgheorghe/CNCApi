<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use Doctrine\DBAL\Connection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class STLTest extends \PHPUnit_Framework_TestCase
{
    private $stlObjectName = 'TEST';
    private $stlFileString = <<<EOT
solid TEST
    facet normal 0 1 2
        outerloop
            vertex -1.793617e-01   -2.566654e-02 5.000000e+00
            vertex 5 4 6
            vertex 7 8 9
        endloop
    endfacet
    facet normal 10 11 12
        outerloop
            vertex 12 14 15
            vertex 16 17 18
            vertex 19 20 21
        endloop
    endfacet
endsolid TEST
EOT;

    private $stlFileArray = array(
        "name" => "TEST",
        "facet-normals" => array()
    );

    private $uploadSQL = "INSERT INTO stl_objects SET stl_object_name = :name, stl_object_status = :status, stl_object_data = :data";
    private $unpackSQL = "UPDATE stl_objects SET stl_object_status = :status, stl_object_coordinates = :coordinates, stl_object_interim_data = :interim WHERE stl_object_name = :name LIMIT 1";

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testContainerStlFileReaderDatabaseConnectionAndQueueConnectionDependenciesAndQueueNameAreSet() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $databaseConnectionMock = \Mockery::mock(Connection::class);
        $queueConnectionMock = \Mockery::mock(AMQPStreamConnection::class);

        // Prepare a mock of the setContainer method.
        $stlLibraryMock = \Mockery::mock(STL::class)
            ->shouldAllowMockingProtectedMethods();

        $stlLibraryMock->shouldReceive('setContainer')
            ->with($containerMock)
            ->once();

        $stlLibraryMock->shouldReceive('setStlFileReader')
            ->with($stlFileReaderMock)
            ->once();

        $stlLibraryMock->shouldReceive('setDatabaseConnection')
            ->with($databaseConnectionMock)
            ->once();

        $stlLibraryMock->shouldReceive('setQueueConnection')
            ->with($queueConnectionMock)
            ->once();

        $stlLibraryMock->shouldReceive('setQueueName')
            ->with('test')
            ->once();

        $stlLibraryMock->shouldReceive('setExchangeName')
            ->with('test')
            ->once();

        // Now get the original constructor.
        $reflectedStlLibraryClass = new \ReflectionClass(STL::class);
        $reflectedStlLibraryClassConstructor = $reflectedStlLibraryClass->getConstructor();

        // Finally, call it against the mocked stl library.
        $reflectedStlLibraryClassConstructor->invoke(
            $stlLibraryMock,
            $containerMock,
            $stlFileReaderMock,
            $databaseConnectionMock,
            $queueConnectionMock,
            'test',
            'test'
        );
    }

    public function testStlFileIsUploadedToDatabaseAndAddedToQueue() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $databaseConnectionMock = \Mockery::mock(Connection::class);
        $queueConnectionMock = \Mockery::mock(AMQPStreamConnection::class);
        $queueChannelMock = \Mockery::mock(AMQPChannel::class);

        $stlFileReaderMock->shouldReceive('getName')
            ->once()
            ->andReturn($this->stlObjectName);

        $stlFileReaderMock->shouldReceive('getStlFileString')
            ->once()
            ->andReturn($this->stlFileString);

        $databaseConnectionMock->shouldReceive('executeQuery')
            ->once()
            ->with($this->uploadSQL, array(
                "name" => $this->stlObjectName,
                "status" => "raw",
                "data" => $this->stlFileString
            ));

        $queueConnectionMock->shouldReceive('channel')
            ->once()
            ->andReturn($queueChannelMock);

        $queueChannelMock->shouldReceive('queue_declare')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', false, true, false, false);

        $queueChannelMock->shouldReceive('exchange_declare')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', 'direct', false, true, false);

        $queueChannelMock->shouldReceive('queue_bind')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', 'test');

        $queueChannelMock->shouldReceive('basic_publish')
            ->once();

        $queueChannelMock->shouldReceive('close')
            ->once();

        $stlLibrary = new STL($containerMock, $stlFileReaderMock, $databaseConnectionMock, $queueConnectionMock, 'test', 'test');
        $stlLibrary->upload();
    }

    public function testStlFileIsConvertedToJsonStringDatabaseRecordIsUpdatedAndFileQueuedForGcode() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $databaseConnectionMock = \Mockery::mock(Connection::class);
        $queueConnectionMock = \Mockery::mock(AMQPStreamConnection::class);
        $queueChannelMock = \Mockery::mock(AMQPChannel::class);

        $stlFileReaderMock->shouldReceive('getName')
            ->once()
            ->andReturn($this->stlObjectName);

        $stlFileReaderMock->shouldReceive('toArray')
            ->once()
            ->andReturn($this->stlFileArray);

        $stlFileReaderMock->shouldReceive('getStlFileString')
            ->twice()
            ->andReturn($this->stlFileString);

        $stlFileReaderMock->shouldReceive('setStlFileStringFromArray')
            ->once()
            ->withAnyArgs();

        $databaseConnectionMock->shouldReceive('executeQuery')
            ->once()
            ->with($this->unpackSQL, array(
                "status" => "coordinates",
                "coordinates" => json_encode($this->stlFileArray),
                "name" => $this->stlObjectName,
                "interim" => $this->stlFileString
            ));

        $queueConnectionMock->shouldReceive('channel')
            ->once()
            ->andReturn($queueChannelMock);

        $queueChannelMock->shouldReceive('queue_declare')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', false, true, false, false);

        $queueChannelMock->shouldReceive('exchange_declare')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', 'direct', false, true, false);

        $queueChannelMock->shouldReceive('queue_bind')
            ->once()
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            ->with('test', 'test');

        $queueChannelMock->shouldReceive('basic_publish')
            ->once();

        $queueChannelMock->shouldReceive('close')
            ->once();

        $stlLibrary = new STL($containerMock, $stlFileReaderMock, $databaseConnectionMock, $queueConnectionMock, 'test', 'test');
        $stlLibrary->unpack();
    }
}