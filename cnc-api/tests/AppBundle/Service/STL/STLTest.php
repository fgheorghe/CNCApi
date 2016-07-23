<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use AppBundle\Service\STL\STLMillingEdit;

class STLTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    public function testContainerStlEditAndStlFileReaderServicesAreSet() {
        // Service and stl file reader container mocks.
        $containerMock = \Mockery::mock(Container::class);
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class);
        $stlMillingEditorMock = \Mockery::mock(STLMillingEdit::class);

        // Prepare a mock of the setContainer method.
        $stlLibraryMock = \Mockery::mock(STL::class)
            ->shouldAllowMockingProtectedMethods();

        $stlLibraryMock->shouldReceive('setContainer')
            ->with($containerMock)
            ->once();

        $stlLibraryMock->shouldReceive('setStlFileReader')
            ->with($stlFileReaderMock)
            ->once();

        $stlLibraryMock->shouldReceive('setStlMillingEditor')
            ->with($stlMillingEditorMock)
            ->once();

        // Now get the original constructor.
        $reflectedStlLibraryClass = new \ReflectionClass(STL::class);
        $reflectedStlLibraryClassConstructor = $reflectedStlLibraryClass->getConstructor();

        // Finally, call it against the mocked stl library.
        $reflectedStlLibraryClassConstructor->invoke(
            $stlLibraryMock,
            $containerMock,
            $stlFileReaderMock,
            $stlMillingEditorMock
        );
    }

    public function testStlFileIsUploadedToDatabase() {
        $this->markTestIncomplete('TODO');
    }
}