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

    private $facetNormal1 = <<<EOT
facet normal 0 1 2
outerloop
vertex 3 4 5
vertex 5 4 6
vertex 7 8 9
endloop
endfacet
EOT;

    private $facetNormal2 = <<<EOT
facet normal 10 11 12
outerloop
vertex 12 14 15
vertex 16 17 18
vertex 19 20 21
endloop
endfacet
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
        $stlFileReader = new STLFileReader(
            $this->stlFileString
        );

        $this->assertEquals(
            array(
                $this->facetNormal1,
                $this->facetNormal2
            ),
            $stlFileReader->getFacetNormals()
        );
    }

    function testFacetNormalCoordinatesAreExtracted() {
        $stlFileReader = new STLFileReader(
            $this->stlFileString
        );

        $this->assertEquals(
            array(
                0,
                1,
                2
            ),
            $stlFileReader->getFacetNormalCoordinates($this->facetNormal1)
        );

        $this->assertEquals(
            array(
                10,
                11,
                12
            ),
            $stlFileReader->getFacetNormalCoordinates($this->facetNormal2)
        );
    }
}