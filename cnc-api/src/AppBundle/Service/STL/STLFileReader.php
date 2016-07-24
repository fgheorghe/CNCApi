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
            // Ignore lines starting with 'solid' and 'endsolid'
            if (substr(trim($line), 0, 5) == "solid" || substr(trim($line), 0, 8) == "endsolid") {
                continue;
            }
            // Ignore empty lines.
            if (empty($line)) {
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

    /**
     * Extracts coordinates from a given facet normal string block.
     *
     * @param string $facetNormal
     * @return array
     */
    public function getFacetNormalCoordinates(string $facetNormal) : array {
        $firstLine = explode("\n",$facetNormal)[0];
        $pattern = "/facet normal +(\-*\d+\.*\d*e*\-*\+*\d*) +(\-*\d+\.*\d*e*\-*\+*\d*) +(\-*\d+\.*\d*e*\-*\+*\d*)/";

        preg_match($pattern, trim($firstLine), $matches);

        return array(
            (float) $matches[1],
            (float) $matches[2],
            (float) $matches[3]
        );
    }

    /**
     * Extracts facet vertex coordinates from a given facet normal string block.
     *
     * @param string $facetNormal
     * @return array
     */
    public function getFacetVertexCoordinates(string $facetNormal) : array {
        $lines = explode("\n", $facetNormal);
        $coordinates = array();

        // Ignore first, second, second to last and last lines, as these define the facet normal start and end and outer
        // loops start and end.
        for ($i = 2; $i < count($lines) - 2; $i++) {
            $pattern = "/vertex +(\-*\d+\.*\d*e*\-*\+*\d*) +(\-*\d+\.*\d*e*\-*\+*\d*) +(\-*\d+\.*\d*e*\-*\+*\d*)/";

            preg_match($pattern, trim($lines[$i]), $matches);
            $coordinates[] = array(
                (float) $matches[1],
                (float) $matches[2],
                (float) $matches[3]
            );
        }

        return $coordinates;
    }

    /**
     * Returns the name of the solid in this file.
     *
     * @return string
     */
    public function getName() : string {
        return explode(" ", trim(explode("\n", $this->getStlFileString())[0]))[1];
    }

    /**
     * Method used for converting the STL file to an associative array.
     *
     * The following keys are set:
     * - name -> name of STL object.
     * -> facet-normals -> numeric array of facet normal blocks, with the following items per each key
     * coordinates -> facet normal coordinates
     * vertices -> numeric array of vertex coordinates arrays
     *
     * @return array
     */
    public function toArray() : array {
        $name = $this->getName();

        $facetNormals = array();

        foreach ($this->getFacetNormals() as $facetNormal) {
            $facet = array(
                "coordinates" => $this->getFacetNormalCoordinates($facetNormal),
                "vertices" => $this->getFacetVertexCoordinates($facetNormal)
            );

            $facetNormals[] = $facet;
        }

        return array(
            "name" => $name,
            "facet-normals" => $facetNormals
        );
    }

    /**
     * Re-packs an STL file array into a string, and sets it as the file content of this object.
     *
     * @param $stlFileArray array
     * @return STLFileReader
     */
    public function setStlFileStringFromArray(array $stlFileArray) : STLFileReader {
        // Set name.
        $stlFileContent = "solid " . $stlFileArray["name"] . "\n";

        foreach ($stlFileArray["facet-normals"] as $facetNormal) {
            $stlFileContent .= "    facet normal " . implode(" ", $facetNormal["coordinates"]) . "\n        outerloop\n";

            foreach ($facetNormal["vertices"] as $vertex) {
                $stlFileContent .= "            vertex " . implode(" ", $vertex) . "\n";
            }

            $stlFileContent .= "        endloop\n    endfacet\n";
        }

        $stlFileContent .= "endsolid " . $stlFileArray["name"];

        $this->setStlFileString($stlFileContent);

        return $this;
    }
}