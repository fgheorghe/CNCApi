<?php

namespace AppBundle\Tests\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use AppBundle\Service\STL\STLMillingEdit;

class STLMillingEditTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }
}