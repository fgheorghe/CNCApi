<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 13/07/16
 * Time: 22:39
 */

namespace AppBundle\Service\STL;
use Symfony\Component\DependencyInjection\Container;

class STL
{
    private $container;

    /**
     * @return mixed
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return STL
     */
    protected function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function __construct(Container $container) {
        $this->setContainer($container);
    }
}