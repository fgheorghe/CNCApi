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
}