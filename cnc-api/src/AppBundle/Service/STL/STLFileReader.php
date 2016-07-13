<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 13/07/16
 * Time: 23:01
 */

namespace AppBundle\Service\STL;

/**
 * Class STLFileReader. Used for parsing STL files contents - not reading disk files.
 * @package AppBundle\Service\STL
 */
class STLFileReader
{
    protected $stlFileString;

    /**
     * @return mixed
     */
    public function getStlFileString() : string
    {
        return $this->stlFileString;
    }

    /**
     * @param mixed $stlFileString
     * @return STLFileReader
     */
    public function setStlFileString(string $stlFileString)
    {
        $this->stlFileString = $stlFileString;
        return $this;
    }

    public function __construct(string $stlFileString)
    {
        $this->setStlFileString($stlFileString);
    }

    /**
     * Extracts facet normals from set string.
     *
     * @return array
     */
    public function getFacetNormals() : array {
        $facetStrings = array();
        $currentFacetString = 0;
        $lines = explode("\n", $this->getStlFileString());

        foreach ($lines as $line) {
            // Ignore lines start with 'solid' and 'endsolid'
            if (substr(trim($line), 0, 5) == "solid" || substr(trim($line), 0, 8) == "endsolid") {
                continue;
            }

            // Append to current facet string.
            $facetStrings[$currentFacetString] = ($facetStrings[$currentFacetString] ?? "") . trim($line) . "\n";

            // Move to next facet if this block is done.
            if (substr(trim($line), 0, 8) == "endfacet") {
                // Remove last EOL.
                $facetStrings[$currentFacetString] = trim($facetStrings[$currentFacetString]);
                // Move to next facet.
                $currentFacetString++;
            }
        }

        return $facetStrings;
    }
}