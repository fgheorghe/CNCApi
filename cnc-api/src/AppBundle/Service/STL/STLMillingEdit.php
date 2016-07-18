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
}