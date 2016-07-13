<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STLFileReader;

class STLFileReaderTest extends \PHPUnit_Framework_TestCase
{
        private $stlFileString = <<<EOT
solid TEST
    facet normal 0 1 2
        outerloop
            vertex 3 4 5
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

    public function testFileStringIsSetByConstructor() {
        $stlFileReaderMock = \Mockery::mock(STLFileReader::class)->shouldAllowMockingProtectedMethods();

        $stlFileReaderMock->shouldReceive('setStlFileString')
            ->with($this->stlFileString)
            ->once();

        $stlFileReaderReflection = new \ReflectionClass(STLFileReader::class);
        $stlFileReaderReflectionConstructor = $stlFileReaderReflection->getConstructor();

        $stlFileReaderReflectionConstructor->invoke(
            $stlFileReaderMock,
            $this->stlFileString
        );
    }

    public function testFacetNormalsAreReturnedAsArray() {
        $this->markTestIncomplete();
    }
}