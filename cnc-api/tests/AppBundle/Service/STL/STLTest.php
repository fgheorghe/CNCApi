<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use Doctrine\DBAL\Connection;

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

    private $uploadSQL = "INSERT INTO stl_objects SET stl_object_name = :name, stl_object_status = :status, stl_object_data = :data";

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testContainerStlEditAndStlFileReaderServicesAreSet() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $databaseConnectionMock = \Mockery::mock(Connection::class);

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

        // Now get the original constructor.
        $reflectedStlLibraryClass = new \ReflectionClass(STL::class);
        $reflectedStlLibraryClassConstructor = $reflectedStlLibraryClass->getConstructor();

        // Finally, call it against the mocked stl library.
        $reflectedStlLibraryClassConstructor->invoke(
            $stlLibraryMock,
            $containerMock,
            $stlFileReaderMock,
            $databaseConnectionMock
        );
    }

    public function testStlFileIsUploadedToDatabase() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $databaseConnectionMock = \Mockery::mock(Connection::class);

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

        $stlLibrary = new STL($containerMock, $stlFileReaderMock, $databaseConnectionMock);
        $stlLibrary->upload();
    }
}