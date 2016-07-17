<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 13/07/16
 * Time: 22:39
 */

namespace AppBundle\Service\STL;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STLFileReader;
use AppBundle\Service\STL\STLEdit;

/**
 * Class STL. Provides STL conversion functionality.
 * @package AppBundle\Service\STL
 */
class STL
{
    private $container;
    private $stlFileReader;
    private $stlEditor;

    /**
     * @return STLEdit
     */
    public function getStlEditor() : STLEdit
    {
        return $this->stlEditor;
    }

    /**
     * @param mixed $stlEditor
     * @return STL
     */
    public function setStlEditor(STLEdit $stlEditor) : STL
    {
        $this->stlEditor = $stlEditor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStlFileReader() : STLFileReader
    {
        return $this->stlFileReader;
    }

    /**
     * @param mixed $stlFileReader
     * @return STL
     */
    public function setStlFileReader(STLFileReader $stlFileReader)
    {
        $this->stlFileReader = $stlFileReader;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getContainer() : Container
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return STL
     */
    protected function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    public function __construct(Container $container, STLFileReader $stlFileReader, STLEdit $stlEditor) {
        $this->setContainer($container);
        $this->setStlFileReader($stlFileReader);
        $this->setStlEditor($stlEditor);
    }
}