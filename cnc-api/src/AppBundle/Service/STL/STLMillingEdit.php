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
    private $length;
    private $height;
    private $width;

    /**
     * @return mixed
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * @param mixed $length
     * @return STLMillingEdit
     */
    public function setLength($length) : STLMillingEdit
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight() : int
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     * @return STLMillingEdit
     */
    public function setHeight($height) : STLMillingEdit
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth() : int
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     * @return STLMillingEdit
     */
    public function setWidth($width) : STLMillingEdit
    {
        $this->width = $width;
        return $this;
    }

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

    public function __construct(int $length, int $height, int $width) {
        $this->setLength($length)
            ->setHeight($height)
            ->setWidth($width);
    }
}