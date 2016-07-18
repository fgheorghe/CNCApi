<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STLMillingEdit;

class STLMillingEditTest extends \PHPUnit_Framework_TestCase
{
    private $testStlArray = array(
        "name" => "TEST2",
        "facet-normals" => array(
            array(
                "coordinates" => array(
                    0,
                    1,
                    2
                ),
                "vertices" => array(
                    array(
                        3, 4, 5
                    ),
                    array(
                        5, 4, 6
                    ),
                    array(
                        7, 8, 9
                    )
                )
            ),
            array(
                "coordinates" => array(
                    10,
                    11,
                    12
                ),
                "vertices" => array(
                    array(
                        12, 14, 15
                    ),
                    array(
                        16, 17, 18
                    ),
                    array(
                        19, 20, 21
                    )
                )
            )
        )
    );

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testFileArrayIsSetByConstructor() {
        $stlMillingEditorMock = \Mockery::mock(STLMillingEdit::class)->shouldAllowMockingProtectedMethods();

        $stlMillingEditorMock->shouldReceive('setStlFileContentArray')
            ->with($this->testStlArray)
            ->once();

        $stlFileReaderReflection = new \ReflectionClass(STLMillingEdit::class);
        $stlFileReaderReflectionConstructor = $stlFileReaderReflection->getConstructor();

        $stlFileReaderReflectionConstructor->invoke(
            $stlMillingEditorMock,
            $this->testStlArray
        );
    }

    public function testLowestVertexX() {
        $stlMillingEditor = new STLMillingEdit($this->testStlArray);

        $this->assertEquals(
            3,
            $stlMillingEditor->getLowestVertexX()
        );
    }

    public function testHighestVertexX() {
        $stlMillingEditor = new STLMillingEdit($this->testStlArray);

        $this->assertEquals(
            19,
            $stlMillingEditor->getHighestVertexX()
        );
    }

    public function testRemoveLowestXVertices() {
        $stlMillingEditor = new STLMillingEdit($this->testStlArray);

        $this->assertEquals(
            array(
                "name" => "TEST2",
                "facet-normals" => array(
                    array(
                        "coordinates" => array(
                            10,
                            11,
                            12
                        ),
                        "vertices" => array(
                            array(
                                12, 14, 15
                            ),
                            array(
                                16, 17, 18
                            ),
                            array(
                                19, 20, 21
                            )
                        )
                    )
                )
            ),
            $stlMillingEditor->removeLowestXVertices()->getStlFileContentArray()
        );
    }

    public function testRemoveHighestXVertices() {
        $stlMillingEditor = new STLMillingEdit($this->testStlArray);

        $this->assertEquals(
            array(
                "name" => "TEST2",
                "facet-normals" => array(
                    array(
                        "coordinates" => array(
                            0,
                            1,
                            2
                        ),
                        "vertices" => array(
                            array(
                                3, 4, 5
                            ),
                            array(
                                5, 4, 6
                            ),
                            array(
                                7, 8, 9
                            )
                        )
                    )
                )
            ),
            $stlMillingEditor->removeHighestXVertices()->getStlFileContentArray()
        );
    }

    public function testExtractMillingContent() {
        $stlMillingEditor = new STLMillingEdit($this->testStlArray);

        $this->assertEquals(
            array(
                "name" => "TEST2",
                "facet-normals" => array()
            ),
            $stlMillingEditor->extractMillingContent()->getStlFileContentArray()
        );
    }
}