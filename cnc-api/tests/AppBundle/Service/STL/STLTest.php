<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STL;

class STLTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    public function testContainerIsSet() {
        // Service container mock.
        $containerMock = \Mockery::mock(Container::class);

        // Prepare a mock of the setContainer method.
        $stlLibraryMock = \Mockery::mock(STL::class)
            ->shouldAllowMockingProtectedMethods();

        $stlLibraryMock->shouldReceive('setContainer')
            ->with($containerMock)
            ->once();

        // Now get the original constructor.
        $reflectedStlLibraryClass = new \ReflectionClass(STL::class);
        $reflectedStlLibraryClassConstructor = $reflectedStlLibraryClass->getConstructor();

        // Finally, call it against the mocked stl library.
        $reflectedStlLibraryClassConstructor->invoke(
            $stlLibraryMock,
            $containerMock
        );
    }
}