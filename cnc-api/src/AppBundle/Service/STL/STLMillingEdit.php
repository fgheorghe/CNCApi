<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 17/07/16
 * Time: 12:55
 */

namespace AppBundle\Service\STL;

/**
 * Class STLMillingEdit. Used for 'chopping' off parts of an STL file.
 *
 * NOTE: Only to be used for milling!
 *
 * @package AppBundle\Service\STL
 */
class STLMillingEdit
{
    private $stlFileContentArray;

    /**
     * @return mixed
     */
    public function getStlFileContentArray() : array
    {
        return $this->stlFileContentArray;
    }

    /**
     * @param array $stlFileContentArray
     * @return STLMillingEdit
     */
    public function setStlFileContentArray(array $stlFileContentArray)
    {
        $this->stlFileContentArray = $stlFileContentArray;
        return $this;
    }

    public function __construct(array $stlFileContentArray) {
        $this->setStlFileContentArray($stlFileContentArray);
    }

    /**
     * Lowest possible X coordinate of a vertex.
     *
     * @return float
     */
    public function getLowestVertexX() : float {
        $min = null;
        foreach ($this->getStlFileContentArray()["facet-normals"] as $facetNormal) {
            for ($i = 0; $i < 3; $i++) {
                if (is_null($min)) {
                    $min = $facetNormal["vertices"][$i][0];
                    continue;
                }
                if ($min > $facetNormal["vertices"][$i][0]) {
                    $min = $facetNormal["vertices"][$i][0];
                }
            }
        }

        return $min;
    }

    /**
     * Highest possible X coordinate of a vertex.
     *
     * @return float
     */
    public function getHighestVertexX() : float {
        $max = null;
        foreach ($this->getStlFileContentArray()["facet-normals"] as $facetNormal) {
            for ($i = 0; $i < 3; $i++) {
                if (is_null($max)) {
                    $max = $facetNormal["vertices"][$i][0];
                    continue;
                }
                if ($max < $facetNormal["vertices"][$i][0]) {
                    $max = $facetNormal["vertices"][$i][0];
                }
            }
        }

        return $max;
    }

    /**
     * Remove all vertices with the lowest X values.
     *
     * @return STLMillingEdit
     */
    public function removeLowestXVertices() : STLMillingEdit {
        $facetNormals = [];
        $contentArray = $this->getStlFileContentArray();

        foreach ($contentArray["facet-normals"] as $facetNormal) {
            $skip = false;
            for ($i = 0; $i < 3; $i++) {
                if ($facetNormal["vertices"][$i][0] == $this->getLowestVertexX()) {
                    $skip = true;
                }
            }
            if (!$skip) {
                $facetNormals[] = $facetNormal;
            }
        }

        $contentArray["facet-normals"] = $facetNormals;

        $this->setStlFileContentArray($contentArray);

        return $this;
    }

    /**
     * Remove all vertices with the highest X values.
     *
     * @return STLMillingEdit
     */
    public function removeHighestXVertices() : STLMillingEdit {
        $facetNormals = [];
        $contentArray = $this->getStlFileContentArray();

        foreach ($contentArray["facet-normals"] as $facetNormal) {
            $skip = false;
            for ($i = 0; $i < 3; $i++) {
                if ($facetNormal["vertices"][$i][0] == $this->getHighestVertexX()) {
                    $skip = true;
                }
            }
            if (!$skip) {
                $facetNormals[] = $facetNormal;
            }
        }

        $contentArray["facet-normals"] = $facetNormals;

        $this->setStlFileContentArray($contentArray);

        return $this;
    }

    /**
     * Extract the actual milling objects.
     *
     * @return STLMillingEdit
     */
    public function extractMillingContent() : STLMillingEdit {
        $this->removeHighestXVertices()->removeLowestXVertices();
        return $this;
    }
}